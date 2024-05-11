                            <div class="row pb10">
                              <div class="col-lg-2 col-md-3 col-sm-12">   
                                <p class="examinfo"><span><?php echo $this->lang->line('exam')?></span><?php echo $examgroupDetail->exam; ?></p>
                              </div> 

                              <div class="col-lg-10 col-md-9 col-sm-12">   
                                <p class="examinfo"><span><?php echo $this->lang->line('exam')." ".$this->lang->line('group'); ?></span><?php echo $examgroupDetail->exam_group_name; ?></p>
                              </div> 
                            </div><!--./row-->
                            <div class="divider2"></div>
                            <div class="table-responsive row">
                              <table class="table table-bordered" id="subjects_table">
                                <thead>
                                    <tr>
                                        <th class="col-sm-3"><?php echo $this->lang->line('subject')?></th>
                                        <th class=""><?php echo $this->lang->line('date')." ".$this->lang->line('from')?></th>
                                        <th class=""><?php echo $this->lang->line('start')." ".$this->lang->line('time'); ?></th>
                                        <th class=""><?php echo $this->lang->line('duration')?></th>
                                        <th class=""><?php echo $this->lang->line('room')." ".$this->lang->line('no')?></th>
                                        <th class=""><?php echo $this->lang->line('marks')." (".$this->lang->line('max').".)";?></th>
                                        <th class=""><?php echo $this->lang->line('marks')." (".$this->lang->line('min').".)";?></th>
                                        <th class="text-right"><?php echo $this->lang->line('enter')." ".$this->lang->line('marks'); ?></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($exam_subjects)) {
                                       
                                        foreach ($exam_subjects as $exam_subject_key => $exam_subject_value) {
                                            ?>
                                            <tr>
                                                <td><?php echo $exam_subject_value->subject_name; ?></td>
                                                <td><?php echo date($this->customlib->getSchoolDateFormat(),strtotime($exam_subject_value->date_from)); ?></td>
                                                <td><?php echo $exam_subject_value->time_from; ?></td>
                                                <td><?php echo $exam_subject_value->duration; ?></td>
                                                <td><?php echo $exam_subject_value->room_no; ?></td>
                                                <td><?php echo $exam_subject_value->max_marks; ?></td>
                                                <td><?php echo $exam_subject_value->min_marks; ?></td>
                                                <td class="col-sm-1 text-right">                                             
                                                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#subjectModal" data-subject_name="<?php echo $exam_subject_value->subject_name; ?>" data-subject_id="<?php echo $exam_subject_value->id; ?>" data-teachersubject_id="<?php echo $exam_subject_value->subject_id; ?>" ><i class="fa fa-newspaper-o" aria-hidden="true"></i></button>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>    