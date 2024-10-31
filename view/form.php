<div class="payg-form-container aligncenter">

	<h3>Australian Government Stimulus<br/>PAYGW Cash Flow Assistance Calculator</h3> <br/>

	<form method="post" id="frmpayg">
		
		<label for="name" class="text-dark">How often do you report your PAYGW?</label><br>
		<label class="radio-inline">
			<input type="radio" checked="checked" value="quarterly" name="paygReporting"> Quarterly
		</label>&nbsp;

		<label class="radio-inline">
			<input type="radio" value="monthly" name="paygReporting"> Monthly
		</label>&nbsp;
		<br/><br/>

		<div class="payg-inputs payg-quarterly">
				<label for="amount3">Jan - Mar (Q3)</label>
				<input type="text" name="quarterly_amount3"  value="" class="form-control" placeholder="PAYGW estimated amount">

				<label for="amount6">Apr - Jun (Q4)</label>
				<input type="text" name="quarterly_amount4"  value="" class="form-control" placeholder="PAYGW estimated amount">
		</div>

		<div class="payg-inputs payg-monthly">
				<label for="amount3">March IAS</label>
				<input type="text" name="month1"  value="" class="form-control" placeholder="PAYGW estimated amount">

				<label for="amount6">April IAS</label>
				<input type="text" name="month2"  value="" class="form-control" placeholder="PAYGW estimated amount"> 

				<label for="amount3">May IAS</label>
				<input type="text" name="month3"  value="" class="form-control" placeholder="PAYGW estimated amount">

				<label for="amount6">June IAS</label>
				<input type="text" name="month4"  value="" class="form-control" placeholder="PAYGW estimated amount">
		</div>
		<br/>
		<input type="submit" name="btnPAYG" id="btnPAYG" value="Calculate" class="btn btn-primary">
	</form>

	<div id="payg-output">
	</div>
</div>