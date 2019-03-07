<link rel="stylesheet" href="css/vendor/select2.css" type="text/css"/>
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tabs-wrapper">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-open-access" data-toggle="tab" aria-expanded="true">Open Access</a></li>
            <li class=""><a href="#tab-close-access" data-toggle="tab" aria-expanded="false">Close Access</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade active in" id="tab-open-access">
              <p>
                Here you can make settings for open access.
              </p>
              <div class="form-group form-group-select2">
                  <label>Multi-Value Select Boxes</label>
                  <select style="width:300px" id="sel2Multi" multiple>
                    <option value="United States">United States</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Afghanistan">Afghanistan</option>
                    <option value="Albania">Albania</option>
                    <option value="Algeria">Algeria</option>
                    <option value="American Samoa">American Samoa</option>
                    <option value="Andorra">Andorra</option>
                    <option value="Angola">Angola</option>
                    <option value="Anguilla">Anguilla</option>
                    <option value="Antarctica">Antarctica</option>
                    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                    <option value="Argentina">Argentina</option>
                    <option value="Armenia">Armenia</option>
                    <option value="Aruba">Aruba</option>
                    <option value="Australia">Australia</option>
                    <option value="Austria">Austria</option>
                    <option value="Azerbaijan">Azerbaijan</option>
                    <option value="Slovakia">Slovakia</option>
                  </select>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-close-access">
              Here you can make settings for close access.
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<script src="js/select2.min.js"></script>
