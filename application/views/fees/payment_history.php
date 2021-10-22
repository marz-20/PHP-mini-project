<?php
$widget = (is_superadmin_loggedin() ? 3 : 4);
$currency_symbol = $global_config['currency_symbol'];
?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
			<div class="panel-body">
				<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id' onchange='getClassByBranch(this.value)'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
						</div>
					</div>
				<?php endif; ?>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('class')?></label>
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
						</div>
					</div>
                	
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('payment_via')?> <span class="required">*</span></label>
							<?php
								$arrayVia = array(
									'' => translate('select'),
									'all' => translate('both'),
									'online' => "Online",
									'cash' => "Cash",
								);
								echo form_dropdown("payment_via", $arrayVia, set_value('payment_via'), "class='form-control' required
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
						</div>
					</div>
                	<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label">Collect By</label>
							<?php
 									$sql = "SELECT * from staff ";
        							$typeDataAll =  $this->db->query($sql)->result_array();
                               		$feesArray = array();
                               		$feesArray[0] = 'Select';
                               		
                               		if(count($typeDataAll)){
                                    	foreach($typeDataAll as $row){
                                        	$feesArray[$row['id']] = $row['name']; 
                                        }
                                    }
								echo form_dropdown("staff_id", $feesArray, set_value('staff_id'), "class='form-control' id='extra_feesType'
								data-plugin-selectTwo data-width='100%' ");
							?>
						</div>
					</div>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?php echo translate('date'); ?> <span class="required">*</span></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-calendar-check"></i></span>
								<input type="text" class="form-control daterange" name="daterange" value="<?php echo set_value('daterange', date("Y/m/d") . ' - ' . date("Y/m/d")); ?>" required />
							</div>
						</div>
					</div>
                <div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('fees_type')?></label>
							<select data-plugin-selectTwo multiple class="form-control" name="fees_type[]" id="feesType" data-plugin-options='{"placeholder": "Select multiple fees"}'>
								<?php
									
        $branchID = $this->application_model->get_branch_id();
        $fees_type = set_value('fees_type');
                            
        if (!empty($branchID)) {
            $this->db->where('session_id', get_session_id());
            $this->db->where('branch_id', $branchID);
        	
            $result = $this->db->get('fee_groups')->result_array();
            if (count($result)) {
                print "<option value=''>" . translate('select') . "</option>";
                foreach ($result as $row) {
                    print '<optgroup label="' . $row['name'] . '">';
                        $this->db->where('fee_groups_id', $row['id']);
                        $resultdetails = $this->db->get('fee_groups_details')->result_array();
                        foreach ($resultdetails as $t) {
                        	$sel = '';
                        	if(is_array($fees_type)){
            					
            					foreach($fees_type as $r){
               	 					$fID = explode("|", $r);
                					
                                	if($t['fee_type_id'] == $fID[1]){
                                    	$sel = 'selected="selected"';
                                    }
                                
                				}
                       			
            				} 
                            
                            print '<option '.$sel.' value="' . $t['fee_groups_id'] . "|" . $t['fee_type_id'] . '">' . get_type_name_by_id('fees_type', $t['fee_type_id']) . '</option>';
                        }
                   print '</optgroup>';
                }
            } else {
                print'<option value="">' . translate('no_information_available') . '</option>';
            }
        } else {
            print '<option value="">' . translate('select_branch_first') . '</option>';
        }
        
								?>
							</select>
						</div>
					</div>
                
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button type="submit" name="search" value="1" class="btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
					</div>
				</div>
			</footer>
			<?php echo form_close();?>
		</section>
<?php if (isset($invoicelist)): ?>
		<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations'];?>" data-appear-animation-delay="100">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?=translate('fees_payment_history');?></h4>
			</header>
			<div class="panel-body">
				<div class="mb-md mt-md">
					<div class="export_title"><?=translate('fees_payment_history')?></div>
					<table class="table table-bordered table-condensed table-hover mb-none tbr-top table-export">
						<thead>
							<tr>
								<th><?=translate('sl')?></th>
								<th><?=translate('student')?></th>
								<th><?=translate('register_no')?></th>
								<th><?=translate('roll')?></th>
								<th><?=translate('date')?></th>
								<th><?=translate('class')?></th>
								<th><?=translate('collect_by')?></th>
								<th><?=translate('payment_via')?></th>
								<th><?=translate('fees_type')?></th>
								<th><?=translate('amount')?></th>
								<th><?=translate('discount')?></th>
								<th><?=translate('fine')?></th>
								<th><?=translate('total')?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							$totalamount = 0;
							$totaldiscount = 0;
							$totalfine = 0;
							$total = 0;
                    		$studentArray = array();
							foreach($invoicelist as $row):
								$totalamount += $row['amount'];
								$totaldiscount += $row['discount'];
								$totalfine += $row['fine'];
								$totalp = ($row['amount'] + $row['fine']) - $row['discount'];
								$total += $totalp;
                    			$studentArray[$row['first_name'] . ' ' . $row['last_name']] = $count;
								?>
							<tr>
								<td><?php echo $count++; ?></td>
								<td><?php echo $row['first_name'] . ' ' . $row['last_name'];?></td>
								<td><?php echo $row['register_no'];?></td>
								<td><?php echo $row['roll'];?></td>
								<td><?php echo _d($row['date']);?></td>
								<td><?php echo $row['class_name'] ." (" . $row['section_name'] . ")";?></td>
								<td><?php 
								if($row['collect_by'] == 'online'){
									echo "Online";
								} else {
									echo get_type_name_by_id('staff', $row['collect_by']);
								} ?></td>
								<td><?php echo $row['pay_via'];?></td>
								<td><?php echo $row['type_name'];?></td>
								<td><?php echo $currency_symbol . $row['amount'];?></td>
								<td><?php echo $currency_symbol . $row['discount'];?></td>
								<td><?php echo $currency_symbol . $row['fine'];?></td>
								<td><?php echo $currency_symbol . number_format($totalp, 2, '.', '');?></td>
						
							</tr>
							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th>Total : <?php echo count($studentArray); ?></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th><?php echo ($currency_symbol . number_format($totalamount, 2, '.', '')); ?></th>
								<th><?php echo ($currency_symbol . number_format($totaldiscount, 2, '.', '')); ?></th>
								<th><?php echo ($currency_symbol . number_format($totalfine, 2, '.', '')); ?></th>
								<th><?php echo ($currency_symbol . number_format($total, 2, '.', '')); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</section>
<?php endif; ?>
	</div>
</div>
<script>

	var branchID = "<?=$branch_id?>";
		var typeID = "<?=set_value('fees_type')?>";
		var classID = "<?=set_value('class_id')?>";
		var sectionID = "<?=set_value('section_id')?>";

		getTypeByBranch(branchID, typeID);
		//getStudentByClass(branchID, classID, sectionID);

$('#branch_id').on('change', function() {
			var branchID = $(this).val();
			//getClassByBranch(branchID);
			getTypeByBranch(branchID);

		});
	function getTypeByBranch(branchID, typeID) {
    var school_csrf_name = $("input[name=school_csrf_name]").val();
		    $.ajax({
		        url: base_url + 'fees/getTypeByBranch',
		        type: 'POST',
		        data: {
                	'school_csrf_name' : school_csrf_name,
		            'branch_id' : branchID,
		            'type_id' : typeID
		        },
		        success: function (data) {
		            $('#feesType').html(data);
		        }
		    });
		}

</script>
