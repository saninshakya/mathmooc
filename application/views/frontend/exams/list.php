<div class="container middlecontent" style="background:#fff">
    <?php
    if (!empty($categories)) {
        foreach ($categories as $category) {
            if ($category->exam) {
                ?>
                <div class="panel-group" id="accordion">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion"  href="#cat-<?php echo $category->id; ?>">
                                    <?php echo $category->name; ?>
                                </a>
                            </h4>
                        </div>
                        <div id="cat-<?php echo $category->id; ?>" class="panel-collapse collapse in">
                            <table class="table table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th width="50%">Exam</th>
                                        <th width="15%">#Attempt</th>
                                        <th width="20%">Action</th>
                                        <th width="15%">Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($category->exam as $exam) { ?>
                                        <tr>
                                            <td><?php echo $exam->name; ?></td>
                                                <td>-</td>
                                                <td><a class="btn btn-success <?php echo (!$this->ion_auth->logged_in()) ? 'toggle-modal' : ''; ?>" href="<?php echo site_url('exams/takeexam/' . $exam->id); ?>">Take Exam</a></td>
                                            <td></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            }
        }
    } else {
        ?>

        <div class="callout callout-info">
            <h4>No exams have been added at the moment !</h4>
        </div>
<?php } ?>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('.c2').addClass('active');
        $("#filter").keyup(function () {

            // Retrieve the input field text and reset the count to zero
            var filter = $(this).val(), count = 0;

            // Loop through the comment list
            $(".panel-group tr").each(function () {

                // If the list item does not contain the text phrase fade it out
                if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                    $(this).fadeOut();

                    // Show the list item if the phrase matches and increase the count by 1
                } else {
                    $(this).show();
                    count++;
                }
            });

            // Update the count
            var numberItems = count;
            //$("#filter-count").text("Number of Comments = "+count);
        });
    })

</script>