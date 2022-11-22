$(function() {
 
 var frmInfo = $('#frmInfo');
    var frmInfoValidator = frmInfo.validate();

    var frmLogin = $('#frmLogin');
    var frmLoginValidator = frmLogin.validate();

    var frmMobile = $('#frmMobile');
    var frmMobileValidator = frmMobile.validate();

    $('#demo').steps({
      onChange: function (currentIndex, newIndex, stepDirection) {
        // step2
        if (currentIndex === 1) {
          if (stepDirection === 'forward') {
            return frmInfo.valid();
          }
          if (stepDirection === 'backward') {
            frmInfoValidator.resetForm();
          }
        }
        // step4
        if (currentIndex === 3) {
          if (stepDirection === 'forward') {
            return frmLogin.valid();
          }
          if (stepDirection === 'backward') {
            frmLoginValidator.resetForm();
          }
        }
        // step5
        if (currentIndex === 4) {
          if (stepDirection === 'forward') {
            return frmMobile.valid();
          }
          if (stepDirection === 'backward') {
            frmMobileValidator.resetForm();
          }
        }
        return true;
      },
      onFinish: function () {
        alert('Wizard Completed');
      }
    });
	
});