<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.1.js" type="text/javascript"></script>
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <?php echo ($this->session->flashdata('error')) ? error_msg($this->session->flashdata('error')) : ''; ?>
        <?php echo ($this->session->flashdata('success')) ? success_msg($this->session->flashdata('success')) : ''; ?>
        <h4>Exam :: <?php echo $activity->activity_name; ?></h4>
        <div class="box box-info">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Custom Tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#questions" data-toggle="tab">Questions</a></li>
                                <li><a href="#add" data-toggle="tab">Add Question</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="questions">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 2%"></th>
                                                <th style="width: 30%">Question</th>
                                                <th style="width: 5%">marks</th>
                                                <th style="width: 10%">Updated Datetime</th>
                                                <th style="width: 15%">Action</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        if (!empty($activity)) {
                                            $count = 1;
                                            foreach ($activity->activities_question as $question) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $count; ?>.</td>
                                                    <td><?php echo $question->question; ?></td>
                                                    <td><?php echo $question->marks; ?></td>
                                                    <td><?php echo date("Y-m-d h:ia", strtotime($question->updated_datetime)); ?></td>
                                                    <td>
                                                        <?php echo edit_btn('admin/activity_questions/edit/' . $question->id); ?>
                                                        <?php echo delete_btn('admin/activity_questions/delete/' . $question->id); ?>
                                                        <?php echo explanation_btn('admin/activity_questions/explanation/' . $question->id); ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                $count++; # code...
                                            }
                                        }
                                        ?>
                                    </table>
                                </div><!-- /.tab-pane -->
                                <div class="tab-pane" id="add">
                                    <?php
                                    echo form_open_multipart($form_action);
                                    echo form_hidden('activity_id', $activity->id);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-6">
                                            <label>Question</label>
                                            <textarea class="form-control editor required" rows="10" name="question"></textarea>
                                        </div>
                                        <div class="form-group col-xs-6">
                                            <label>Marks</label>
                                            <input type="text" class="form-control required digits" id="q-marks" name="marks" value="" />
                                        </div>
                                        <div class="form-group col-xs-6">
                                            <label>Image</label>
                                            <input type="file" class="form-control" name="que_img" />
                                        </div>
                                    </div>
                                    <h4>Answers</h4><hr>
                                    <div class="row">
                                        <div class="form-group col-xs-6">
                                            <label>Answer</label>
                                            <textarea class="form-control editor required" rows="5" name="answer-1"></textarea>
                                        </div>
                                        <div class="form-group col-xs-3" style="padding-top: 80px;"> 
                                            <label>Correct</label>
                                            <select class="form-control required correct-1" name="correct-1" >
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xs-6">
                                            <label>Answer</label>
                                            <textarea class="form-control editor required" rows="5" name="answer-2"></textarea>
                                        </div>
                                        <div class="form-group col-xs-3" style="padding-top: 80px;"> 
                                            <label>Correct</label>
                                            <select class="form-control required correct-2" name="correct-2">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xs-6">
                                            <label>Answer</label>
                                            <textarea class="form-control editor required" rows="5" name="answer-3"></textarea>
                                        </div>
                                        <div class="form-group col-xs-3" style="padding-top: 80px;"> 
                                            <label>Correct</label>
                                            <select class="form-control required correct-3" name="correct-3">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xs-6">
                                            <label>Answer</label>
                                            <textarea class="form-control editor required" rows="5" name="answer-4"></textarea>
                                        </div>
                                        <div class="form-group col-xs-3" style="padding-top: 80px;"> 
                                            <label>Correct</label>
                                            <select class="form-control required correct-4" name="correct-4">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="box-footer clearfix">
                                        <div class="form-group col-xs-12">
                                            <button type="submit" class="btn btn-primary  pull-right">Submit</button>
                                        </div>
                                    </div>
                                    </form>
                                </div><!-- /.tab-pane -->

                            </div><!-- /.tab-content -->
                        </div><!-- nav-tabs-custom -->
                    </div><!-- /.col -->
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="explanation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Explanation</h4>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.correct-1').val(1);
        $('.correct-2').val(0);
        $('.correct-3').val(0);
        $('.correct-4').val(0);
        $('#q-marks').val('10');
    });
</script>