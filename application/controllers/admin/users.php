<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Backend_Controller {

    protected $activemenu = 'users';

    public function __construct() {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('form_validation');
        $this->template->set('activemenu', $this->activemenu);
    }

    public function index() {
//        $data['users'] = User::find('all', array('order' => 'id DESC'));
        $sql = "SELECT u.active, u.company, u.id, u.username, u.first_name, u.last_name, ug.user_id, ug.group_id,
	ps.parent_id,
	( SELECT CONCAT(u1.first_name,' ', u1.last_name)FROM users AS u1
		WHERE
			u1.id = ps.parent_id
	)AS parent_name
FROM
	users_groups AS ug
INNER JOIN users AS u ON(
	ug.user_id = u.id
)
LEFT JOIN parents_students AS ps ON(
	ps.student_id = ug.user_id
)
WHERE
	ug.group_id = 2";
        $query = $this->db->query($sql);
        $query->result_array();
        $data = get_object_vars($query);
        
        $data['users'] = $data['result_array'];
        unset($data['result_array']);
        $this->template->title('Administrator Panel : manage users')
                ->set_layout($this->admin_tpl)
                ->set('page_title', 'Users')
                ->build($this->admin_folder . '/users/list', $data);
    }

    public function create() {
        //validate form input
        $tables = $this->config->item('tables', 'ion_auth');

        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'xss_clean');
        $this->form_validation->set_rules('username', $this->lang->line('create_user_validation_username_label'), 'required|valid_username|is_unique[' . $tables['users'] . '.username]');
        $this->form_validation->set_rules('user_type', 'user type', 'required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

        if ($this->form_validation->run() == true) {

            $username = strtolower($this->input->post('username'));
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $user_type = $this->input->post('user_type');

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
            );
            $user_id = $this->ion_auth->register($username, $password, $email, $additional_data);
            if ($user_id) {
                $this->ion_auth->add_to_group($user_type, $user_id);
                //check to see if we are creating the user
                //redirect them back to the admin page
                $this->session->set_flashdata('success', $this->ion_auth->messages());
                redirect("admin/users/create", 'refresh');
            } else {
                $message = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $this->session->set_flashdata('message', $message);
                redirect("admin/users/create", 'refresh');
            }
        } else {
            $this->data['groups'] = $this->ion_auth->groups()->result_array();
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['first_name'] = array(
                'name' => 'first_name',
                'id' => 'first_name',
                'type' => 'text',
                'class' => "form-control required",
                'value' => $this->form_validation->set_value('first_name'),
            );
            $this->data['last_name'] = array(
                'name' => 'last_name',
                'id' => 'last_name',
                'type' => 'text',
                'class' => "form-control required",
                'value' => $this->form_validation->set_value('last_name'),
            );
            $this->data['email'] = array(
                'name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'class' => "form-control required email",
                'value' => $this->form_validation->set_value('email'),
            );
            $this->data['username'] = array(
                'name' => 'username',
                'id' => 'username',
                'type' => 'text',
                'class' => "form-control",
                'value' => $this->form_validation->set_value('username'),
            );
            $this->data['phone'] = array(
                'name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'class' => "form-control",
                'value' => $this->form_validation->set_value('phone'),
            );
            $this->data['password'] = array(
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'class' => "form-control required",
                'maxlength' => $this->config->item('max_password_length', 'ion_auth'),
                'minlength' => $this->config->item('min_password_length', 'ion_auth'),
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['password_confirm'] = array(
                'name' => 'password_confirm',
                'id' => 'password_confirm',
                'type' => 'password',
                'class' => "form-control required",
                'equalTo' => '#password',
                'value' => $this->form_validation->set_value('password_confirm'),
            );

            $this->template->title('Administrator Panel : Create User')
                    ->set_layout('admin_tpl')
                    ->set_partial('styles', 'backend/partials/css')
                    ->set_partial('header', 'backend/partials/header')
                    ->set_partial('sidebar', 'backend/partials/sidebar')
                    ->set_partial('footer', 'backend/partials/footer')
                    ->set('page_title', 'Create User')
                    ->set('form_action', 'admin/users/create')
                    ->build($this->admin_folder . '/users/create', $this->data);
        }
    }

    public function edit($id) {
        $user = $this->ion_auth->user($id)->row();
        $groups = $this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();

        //validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'xss_clean');
        $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'xss_clean');
        $this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');

        if (isset($_POST) && !empty($_POST)) {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                show_error($this->lang->line('error_csrf'));
            }

            //update the password if it was posted
            if ($this->input->post('password')) {
                $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
            }

            if ($this->form_validation->run() === TRUE) {
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone'),
                );

                //update the password if it was posted
                if ($this->input->post('password')) {
                    $data['password'] = $this->input->post('password');
                }

                $this->ion_auth->update($user->id, $data);

                // Only allow updating groups if user is admin
                //if ($this->ion_auth->is_admin())
                //{
                //Update the groups user belongs to
                $user_type = $this->input->post('user_type');
                $this->ion_auth->remove_from_group('', $id);
                $this->ion_auth->add_to_group($user_type, $id);
                //}
                //check to see if we are creating the user
                //redirect them back to the admin page
                $this->session->set_flashdata('success', "User Saved");
                redirect('admin/users/edit/' . $user->id, 'refresh');
            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        //pass the user to the view
        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;

        $this->data['first_name'] = array(
            'name' => 'first_name',
            'id' => 'first_name',
            'type' => 'text',
            'class' => "form-control required",
            'value' => $this->form_validation->set_value('first_name', $user->first_name),
        );
        $this->data['last_name'] = array(
            'name' => 'last_name',
            'id' => 'last_name',
            'type' => 'text',
            'class' => "form-control required",
            'value' => $this->form_validation->set_value('last_name', $user->last_name),
        );
        $this->data['email'] = array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'class' => "form-control",
            'readonly' => 'readonly',
            'value' => $this->form_validation->set_value('email', $user->email),
        );
        $this->data['username'] = array(
            'name' => 'username',
            'id' => 'username',
            'type' => 'text',
            'class' => "form-control",
            'value' => $this->form_validation->set_value('username', $user->username),
        );
        $this->data['phone'] = array(
            'name' => 'phone',
            'id' => 'phone',
            'type' => 'text',
            'class' => "form-control",
            'value' => $this->form_validation->set_value('phone', $user->phone),
        );
        $this->data['password'] = array(
            'name' => 'password',
            'id' => 'password',
            'class' => "form-control",
            'maxlength' => $this->config->item('max_password_length', 'ion_auth'),
            'minlength' => $this->config->item('min_password_length', 'ion_auth'),
            'type' => 'password'
        );
        $this->data['password_confirm'] = array(
            'name' => 'password_confirm',
            'id' => 'password_confirm',
            'class' => "form-control",
            'type' => 'password'
        );

        $this->template->title('Administrator Panel : Edit User')
                ->set_layout('admin_tpl')
                ->set_partial('styles', 'backend/partials/css')
                ->set_partial('header', 'backend/partials/header')
                ->set_partial('sidebar', 'backend/partials/sidebar')
                ->set_partial('footer', 'backend/partials/footer')
                ->set('page_title', 'Edit User')
                ->build($this->admin_folder . '/users/edit', $this->data);
    }

    public function view($id) {
        $user = User::find($id);
        $this->template->title('Administrator Panel : view user')
                ->set_layout($this->modal_tpl)
                ->set('page_title', 'View User')
                ->set('user', $user)
                ->set('groups', $this->ion_auth->get_users_groups($id)->result())
                ->build($this->admin_folder . '/users/view');
    }

    public function delete($id) {
        $user = User::find($id);
        if (is_file(PROFILEPHOTOS . $user->photo)) {
            unlink(PROFILEPHOTOS . $user->photo);
            unlink(PROFILEPHOTOSTHUMBS . $user->photo);
        }
        $user->delete();
    }

    public function _get_csrf_nonce() {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    public function _valid_csrf_nonce() {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
                $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deactivate($id = NULL) {
        $id = (int) $id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
        $this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

        if ($this->form_validation->run() == FALSE) {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();

            $this->template->title('Administrator Panel : Deactivate user')
                    ->set_layout('modal_tpl')
                    ->set_partial('styles', 'backend/partials/css')
                    ->set_partial('header', 'backend/partials/header')
                    ->set_partial('sidebar', 'backend/partials/sidebar')
                    ->set_partial('footer', 'backend/partials/footer')
                    ->set('page_title', 'Deactivate User')
                    ->build('auth/deactivate_user', $this->data);
        } else {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes') {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                    show_error($this->lang->line('error_csrf'));
                }
                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->deactivate($id);
                    $this->session->set_flashdata('success', $this->ion_auth->messages());
                }
            }
            //redirect them back to the auth page
            redirect('admin/users');
        }
    }

    //activate the user
    function activate($id) {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            //redirect them to the auth page
            $this->session->set_flashdata('success', $this->ion_auth->messages());
            redirect("admin/users");
        } else {
            //redirect them to the forgot password page
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            redirect("admin/users");
        }
    }

    public function create_parent() {
        
        //validate form input
        $tables = $this->config->item('tables', 'ion_auth');

        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'xss_clean');
        $this->form_validation->set_rules('username', $this->lang->line('create_user_validation_username_label'), 'required|valid_username|is_unique[' . $tables['users'] . '.username]');
        $this->form_validation->set_rules('user_type', 'user type', 'required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

        if ($this->form_validation->run() == true) {
            

            $username = strtolower($this->input->post('username'));
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $user_type = $this->input->post('user_type');
            $student_id = $this->input->post('student_id');
            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone' => $this->input->post('phone'),
            );
            $user_id = $this->ion_auth->register($username, $password, $email, $additional_data);
            if ($user_id) {
                $this->ion_auth->add_to_group($user_type, $user_id);
                //check to see if we are creating the user
                //redirect them back to the admin page
                
                $par_stu = array(
                    'student_id' => $student_id,
                    'parent_id' => $user_id,
                );
                ParentStudent::create($par_stu);
                $this->session->set_flashdata('success', $this->ion_auth->messages());
                redirect("admin/users/create", 'refresh');
            } else {
                $message = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $this->session->set_flashdata('message', $message);
                redirect("admin/users/create", 'refresh');
            }
        } else {
            $this->data['groups'] = $this->ion_auth->groups()->result_array();
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['first_name'] = array(
                'name' => 'first_name',
                'id' => 'first_name',
                'type' => 'text',
                'class' => "form-control required",
                'value' => $this->form_validation->set_value('first_name'),
            );
            $this->data['last_name'] = array(
                'name' => 'last_name',
                'id' => 'last_name',
                'type' => 'text',
                'class' => "form-control required",
                'value' => $this->form_validation->set_value('last_name'),
            );
            $this->data['email'] = array(
                'name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'class' => "form-control required email",
                'value' => $this->form_validation->set_value('email'),
            );
            $this->data['username'] = array(
                'name' => 'username',
                'id' => 'username',
                'type' => 'text',
                'class' => "form-control",
                'value' => $this->form_validation->set_value('username'),
            );
            $this->data['phone'] = array(
                'name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'class' => "form-control",
                'value' => $this->form_validation->set_value('phone'),
            );
            $this->data['password'] = array(
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'class' => "form-control required",
                'maxlength' => $this->config->item('max_password_length', 'ion_auth'),
                'minlength' => $this->config->item('min_password_length', 'ion_auth'),
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['password_confirm'] = array(
                'name' => 'password_confirm',
                'id' => 'password_confirm',
                'type' => 'password',
                'class' => "form-control required",
                'equalTo' => '#password',
                'value' => $this->form_validation->set_value('password_confirm'),
            );
            $this->db->select("ug.user_id, ug.group_id, u.username, CONCAT((u.first_name),(' '),(u.last_name)) as full_name")
                ->from('users_groups AS ug')
                ->join('users AS u', 'u.id = ug.user_id', 'INNER')
                ->where("ug.group_id =", '2');
            $query = $this->db->get();
            $this->data['students'] = $query->result();
            
            $this->template->title('Administrator Panel : Create User')
                    ->set_layout('admin_tpl')
                    ->set_partial('styles', 'backend/partials/css')
                    ->set_partial('header', 'backend/partials/header')
                    ->set_partial('sidebar', 'backend/partials/sidebar')
                    ->set_partial('footer', 'backend/partials/footer')
                    ->set('page_title', 'Create User')
                    ->set('form_action', 'admin/users/create_parent')
                    ->build($this->admin_folder . '/users/create_parent', $this->data);
        }
    }

}
