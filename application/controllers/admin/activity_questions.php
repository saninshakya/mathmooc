<?php

class Activity_questions extends Backend_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function manage($activity_id) {
        $activity = Activity::find($activity_id, array('include' => array('activities_question')));
        $this->template->title('Administrator Panel : manage questions')
                ->set_layout($this->admin_tpl)
                ->set('page_title', 'Manage Questions')
                ->set('form_action', 'admin/activity_questions/create/')
                ->set('activity', $activity)
                ->build($this->admin_folder . '/activity_questions/list');
    }

    public function create() {
        if ($_POST) {
            unset($_POST['_wysihtml5_mode']);
            $activity_id = $this->input->post('activity_id');
            //upload question image if any
            $config['upload_path'] = QUEIMGS;
            $config['allowed_types'] = 'gif|jpg|png';
            $config['width'] = '300';

            $this->load->library('upload', $config);
            $image = '';
            if (!$this->upload->do_upload('que_img')) {
                $error = $this->upload->display_errors('', ' ');
                if ($error != "You did not select a file to upload.") {
                    //$this->session->set_flashdata('error', $error);
                } else {
                    $this->session->set_flashdata('error', $error);
                }
            } else {
                $data = array('upload_data' => $this->upload->data());
                $image = QUEIMGS . $data['upload_data']['file_name'];
            }

            try {
                $question = new ActivitiesQuestion(
                        array('activity_id' => $activity_id,
                    'question' => $_POST['question'],
                    'image' => $image,
                    'created_datetime' => date_time_zone(),
                    'updated_datetime' => date_time_zone(),
                    'marks' => $_POST['marks']));

                $question->save();
                //saving the answer
                for ($count = 1; $count <= 4; $count++) {
                    $answer = new ActivitiesAnswer();
                    $answer->question_id = $question->id;
                    $answer->answer = $this->remove_empty_tags_recursive($_POST['answer-' . $count]);
                    $answer->correct = $_POST['correct-' . $count];
                    $answer->created_datetime = date_time_zone();
                    $answer->updated_datetime = date_time_zone();
                    $answer->save();
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            $this->session->set_flashdata('success', 'Question has been added');
            redirect('admin/activity_questions/manage/' . $activity_id);
        }
    }

    public function remove_empty_tags_recursive($str, $repto = NULL) {
        //** Return if string not given or empty.
        if (!is_string($str) || trim($str) == '')
            return $str;

        //** Recursive empty HTML tags.
        return preg_replace(
                //** Pattern written by Junaid Atari.
                '/<([^<\/>]*)>([\s]*?|(?R))<\/\1>/imsU',
                //** Replace with nothing if string empty.
                !is_string($repto) ? '' : $repto,
                //** Source string
                $str
        );
    }

    public function edit($id) {
        $question = ActivitiesQuestion::find($id, array('include' => array('answer')));
        pretty($question);
    }

}

?>