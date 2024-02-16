
<form method="post" class="batch_dwn_form" id="request_form" data-toggle="validator">
  <div class="batch_dwn_input_group">
    <label for="firstname">{'First name'|translate}<span>*</span></label>
    <input type="text" name="firstname" id="firstname" size="60" required>
    <div class="error-block firstname"></div>
  </div>
  <div class="batch_dwn_input_group">
    <label for="lastname">{'Last name'|translate}<span>*</span></label>
    <input type="text" name="lastname" id="lastname" size="60" required>
    <div class="error-block lastname"></div>
  </div>
  <div class="batch_dwn_input_group">
    <label for="email">{'Email'|translate}<span>*</span></label>
    <input type="email" name="email" id="email" size="60" required>
        <div class="error-block email"></div>
  </div>
  <div class="batch_dwn_input_group">
    <label for="telephone">{'Telephone number'|translate}<span>*</span></label>
    <input type="text" name="telephone" id="telephone" size="60" required>
        <div class="error-block telephone"></div>
  </div>
  <div class="batch_dwn_input_group">
    <label for="organisation">{'Organisation'|translate}<span>*</span></label>
    <input type="text" name="organisation" id="organisation" size="60" required>
    <div class="error-block organisation"></div>
  </div>
  <div class="batch_dwn_input_group">
    <label for="profession">{'Profession'|translate}<span>*</span></label>
    <input type="text" name="profession" id="profession" size="60" required>
    <div class="error-block profession"></div>
  </div>
  <div class="batch_dwn_input_group">
    <label for="reason">{'What are you going to use these photos for?'|translate}<span>*</span></label>
    <input type="text" name="reason" id="reason" size="200" required>
    <div class="error-block reason"></div>
  </div>
  <div class="batch_dwn_input_group" id="batch_dwn_select_size">
     <label for="batch_dwn_request_size">{'Please choose a photo size for this set'|translate}<span>*</span></label>
{html_options name=batch_dwn_request_size options=$batch_dwn_request_size selected="original" id="batch_dwn_request_size"}
  </div>
  <input type="hidden" name="submit_request">
</form>

