<div class="row">
    <!-- left column -->
    <div class="col-md-12">

        <div class="box box-primary">
            <?php echo ($this->session->flashdata('error')) ? error_msg($this->session->flashdata('error')) : ''; ?>
            <?php echo ($this->session->flashdata('success')) ? success_msg($this->session->flashdata('success')) : ''; ?>
            <?php echo (isset($message) && $message != '') ? error_msg($message) : ''; ?>

            <?php echo form_open(uri_string()); ?>

            <div class="box-body">
                <div class="row">
                    <div class="col-xs-6"> 
                        <div class="form-group col-xs-12">
                            <label>First Name</label>
                            <?php echo form_input($first_name); ?>
                        </div>

                        <div class="form-group col-xs-12">
                            <label>Last Name</label>
                            <?php echo form_input($last_name); ?>
                        </div>

                        <div class="form-group col-xs-12">
                            <label>Email</label>
                            <?php echo form_input($email); ?>
                        </div>

                        <div class="form-group col-xs-12">
                            <label>Company</label>
                            <?php echo form_input($company); ?>
                        </div>
                    </div>
                    <div class="col-xs-6">

                        <div class="form-group col-xs-12">
                            <label>Phone</label>
                            <?php echo form_input($phone); ?>
                        </div>

                        <div class="form-group col-xs-12">
                            <label>User Type</label>
                            <?php $user_type = $currentGroups[0]->id; ?>
                            <select name="user_type" class="form-control required">
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?php echo $group['id']; ?>" <?php echo ($user_type == $group['id']) ? 'selected' : ''; ?> ><?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group col-xs-12">
                            <label>Password (Leave blank if not changing password)</label>
                            <?php echo form_input($password); ?>
                        </div>

                        <div class="form-group col-xs-12">
                            <label>Password Confirmation</label>
                            <?php echo form_input($password_confirm); ?>
                        </div>

                        <?php echo form_hidden('id', $user->id); ?>
                        <?php echo form_hidden($csrf); ?>
                    </div>
                </div>
            </div>

            <div class="box-footer clearfix">
                <div class="form-group col-xs-12">
                    <button type="submit" class="btn btn-primary  pull-right">Submit</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
