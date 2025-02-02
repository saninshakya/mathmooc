<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <?php echo ($this->session->flashdata('error')) ? error_msg($this->session->flashdata('error')) : ''; ?>
        <?php echo ($this->session->flashdata('success')) ? success_msg($this->session->flashdata('success')) : ''; ?>
        <div class="box box-info">
            <div class="box-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 2%"></th>
                            <th style="width: 10%">Name</th>
                            <th style="width: 20%">Description</th>
                            <th style="width: 5%">Questions</th>
                            <th style="width: 15%">Action</th>
                        </tr>
                    </thead>
                    <?php
                    if (!empty($activities)) {
                        $count = 1;
                        foreach ($activities as $activity) {
                            ?>
                            <tr>
                                <td><?php echo $count; ?>.</td>
                                <td><?php echo $activity->activity_name; ?></td>
                                <td><?php echo limit_text($activity->description); ?></td>
                                <td><a href="<?php echo site_url('admin/activity_questions/manage/' . $activity->id); ?>" class="btn btn-xs bg-maroon"> Manage</a></td>
                                <td>
                                    <?php echo view_btn('admin/activities/view/' . $activity->id); ?>
                                    <?php echo edit_btn('admin/activities/edit/' . $activity->id); ?>
                                    <?php echo delete_btn('admin/activities/delete/' . $activity->id); ?>
                                </td>
                            </tr>
                            <?php
                            $count++;
                        }
                    }
                    ?>

                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>