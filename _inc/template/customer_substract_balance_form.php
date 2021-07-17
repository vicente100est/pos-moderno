<form id="balance-form" class="form-horizontal" action="customer.php" method="post">
  	<input type="hidden" id="action_type" name="action_type" value="SUBSTRACTBALANCE">
  	<input type="hidden" id="customer_id" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
  	<div class="box-body">
  		<div class="form-group">
			<label for="balance" class="col-sm-3 control-label">
			  <?php echo trans('label_balance'); ?>
			</label>
			<div class="col-sm-7">
			  <p><?php echo currency_format(get_customer_balance($customer['customer_id']));?></p>
			</div>
		</div>
		<div class="form-group">
			<label for="amount" class="col-sm-3 control-label">
			  <?php echo trans('label_amount'); ?>
			</label>
			<div class="col-sm-7">
			  <input type="text" class="form-control" id="amount" name="amount" placeholder="amount" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onKeyUp="if(this.value<0){this.value='1';}" required>
			</div>
		</div>
		<div class="form-group">
			<label for="note" class="col-sm-3 control-label">
			  <?php echo trans('label_note'); ?>
			</label>
			<div class="col-sm-7">
			  <textarea class="form-control" id="note" name="note" placeholder="note here"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-7">
			  <button id="submit-balance" data-form="#balance-form" class="btn btn-info" name="btn_susbtract_balance" data-loading-text="Saving...">
			    <span class="fa fa-fw fa-money"></span>
			    <?php echo trans('button_submit'); ?>
			  </button>
			</div>
		</div>
  	</div>
</form>