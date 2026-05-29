/**
 * remarks.js — Single source of truth for feedback_form / lead_form pages.
 *
 * Expects `base_url` to be defined in the view BEFORE this file loads:
 *   var base_url = "<?php echo base_url(); ?>";
 *
 * Also expects `branch_id` hidden field to be populated from the URL
 * (handled inline in the view since it requires PHP-context URL).
 */

/* ─── Utility ─────────────────────────────────────────────────────────────── */

function url_params() {
  var path   = window.location.pathname;
  var params = path.split('php/');
  return { pathname: path, route: params[1] || '' };
}

var ctrl_page = url_params().route.split('/'); // [controller, action, ...]

/* ─── Country Code ─────────────────────────────────────────────────────────── */

function get_country_code() {
  var my_Date = new Date();
  $.ajax({
    url: base_url + 'index.php/refer_customer/get_country_code?nocache=' +
         my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      var $sel     = $('#country_code');
      var defaultCode = '';

      $.each(data, function(i, item) {
        var code        = String(item.mob_code);
        var displayCode = code.charAt(0) === '+' ? code : '+' + code;

        if (item.is_default == 1) defaultCode = code;

        $sel.append(
          $('<option></option>')
            .attr('value', code)
            .attr('data-id_country', item.id_country)
            .attr('data-code', displayCode)
            .text(displayCode + ' - ' + item.name + ' (' + item.sortname + ')')
        );
      });

      $sel.select2({
        placeholder: 'Code',
        allowClear: true,
        width: '100%',
        minimumResultsForSearch: 2,
        templateSelection: function(data) {
          if (!data.id) return data.text;
          return $(data.element).data('code') || data.text;
        }
      });

      if (defaultCode && $sel.length) {
        $sel.val(defaultCode).trigger('change');
      }
    }
  });
}

/* ─── State ────────────────────────────────────────────────────────────────── */

/**
 * @param {string|number} defaultStateId  - optional state id to pre-select
 * @param {function}      callback        - optional callback after state is set (receives selected state id)
 */
function get_state(defaultStateId, callback) {
  var $sel = $('#cus_state');
  $sel.find('option').remove();

  $.ajax({
    type: 'POST',
    url: base_url + 'index.php/refer_customer/get_state',
    data: { id_country: 101 },
    dataType: 'json',
    success: function(states) {
      $.each(states, function(i, s) {
        $sel.append($('<option></option>').attr('value', s.id).text(s.name));
      });

      $sel.select2({ placeholder: 'Enter State', allowClear: true });

      if (defaultStateId) {
        $sel.val(defaultStateId).trigger('change');
      }

      if (typeof callback === 'function') callback(defaultStateId);
    },
    error: function() {}
  });
}

/* ─── City ─────────────────────────────────────────────────────────────────── */

/**
 * @param {string|number} id_state       - state to load cities for
 * @param {string|number} defaultCityId  - optional city id to pre-select
 */
function get_city(id_state, defaultCityId) {
  var $sel = $('#cus_city');
  $sel.find('option').remove();

  $.ajax({
    type: 'POST',
    url: base_url + 'index.php/refer_customer/get_city',
    data: { id_state: id_state },
    dataType: 'json',
    success: function(cities) {
      $.each(cities, function(i, c) {
        $sel.append($('<option></option>').attr('value', c.id).text(c.name));
      });

      $sel.select2({ placeholder: 'Enter City', allowClear: true });

      if (defaultCityId) {
        $sel.val(defaultCityId).trigger('change.select2');
      }
    },
    error: function() {}
  });
}

/* ─── Employees by Branch ──────────────────────────────────────────────────── */

function loadEmployees() {
  var bid = $('#branch_id').val();
  if (!bid) return;

  $.ajax({
    url: base_url + 'index.php/refer_customer/get_employees_by_branch',
    type: 'POST',
    data: { branch_id: bid },
    dataType: 'json',
    success: function(res) {
      var $sel = $('#staff_id');
      $sel.find('option:not(:first)').remove();
      if (res && res.length) {
        $.each(res, function(i, emp) {
          $sel.append('<option value="' + emp.id_employee + '">' + emp.emp_data + '</option>');
        });
        $sel.trigger('change.select2');
      }
    }
  });
}

/* ─── Customer Search ──────────────────────────────────────────────────────── */

function getSearchCustomers(searchTxt) {
  var my_Date = new Date();
  $.ajax({
    url: base_url + 'index.php/refer_customer/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),
    type: 'POST',
    dataType: 'json',
    data: { searchTxt: searchTxt },
    success: function(data) {
      $('#cus_mobile').autocomplete({
        source: data,
        select: function(e, i) {
          e.preventDefault();
          $('#cus_mobile').val(i.item.label);
          $('#cus_id').val(i.item.value);
          $('#cus_name').val(i.item.firstname);
          $('#cus_mail').val(i.item.email);
          $('#cus_dob').val(i.item.date_of_birth);
          // Auto-set marital status if customer has an anniversary date
          if (i.item.date_of_wed && i.item.date_of_wed !== '' && i.item.date_of_wed !== '0000-00-00') {
            $('#marital_status').val('Yes').trigger('change');
            $('#cus_doa').val(i.item.date_of_wed);
          } else {
            $('#marital_status').val('').trigger('change');
          }
          $('#cus_pincode').val(i.item.pincode);
          $('#address').val(i.item.address1);
          $('#is_existing_customer').val(1);

          // Set pending city BEFORE changing state (state change triggers get_city)
          if (i.item.cus_city) {
            $('#ed_city').val(i.item.cus_city);
          } else {
            $('#ed_city').val('');
          }

          if (i.item.cus_state) {
            $('#cus_state').val(i.item.cus_state).trigger('change');
          }
        },
        change: function(event, ui) {
          if (!ui.item) {
            $('#cus_id').val('');
            $('#cus_name').val('');
            $('#cus_mail').val('');
            $('#cus_dob').val('');
            $('#cus_doa').val('');
            $('#cus_pincode').val('');
            $('#address').val('');
            $('#is_existing_customer').val(0);
          }
        },
        response: function(e, i) {
          if (searchTxt !== '' && i.content.length === 0) {
            $('#cus_id').val('');
            $('#cus_name').val('');
            $('#cus_mail').val('');
            $('#cus_dob').val('');
            $('#cus_doa').val('');
            $('#cus_pincode').val('');
            $('#address').val('');
            $('#is_existing_customer').val(0);
          }
        },
        minLength: 3
      });
    }
  });
}

/* ─── Save Feedback ────────────────────────────────────────────────────────── */

function customer_feedback_save() {
  var my_Date = new Date();

  // Build mobile with country code
  var cc = $('#country_code').val() || '91';
  $('#mobile').val('+' + cc + $('#cus_mobile').val());

  $.ajax({
    url: base_url + 'index.php/refer_customer/customer_feedback/save?nocache=' + my_Date.getUTCSeconds(),
    type: 'POST',
    dataType: 'json',
    data: $('#feedback_form').serialize(),
    success: function(res) {
      if (res.status) {
        $.toaster({ priority: 'success', title: 'Thank You!', message: 'Feedback submitted! Redirecting to Google Reviews...' });

        // Clear form data immediately
        $('#feedback_form')[0].reset();
        $('#staff_rating_wrap').hide();
        $('#rating_staff').val(0);
        $('#rating_ambiance').val(0);
        $('#rating_collection').val(0);
        $('.star-rating i').removeClass('selected hovered');
        $('#cus_state').val('').trigger('change.select2');
        $('#cus_city').find('option').remove().end().trigger('change.select2');
        $('#country_code').val('91').trigger('change.select2');
        $('#purchase_occasion').val('').trigger('change.select2');
        $('#staff_id').val('').trigger('change.select2');
        $('#is_existing_customer').val(0);
        $('#cus_id').val('');
        $('#marital_status').val('');
        $('#anniversary_wrap').hide();
        $('#cus_doa').val('');

        setTimeout(function() {
          var DEFAULT_REVIEW_URL = 'https://www.google.com/search?q=logimax&oq=logimax&gs_lcrp=EgZjaHJvbWUqCggAEAAY4wIYgAQyCggAEAAY4wIYgAQyDQgBEC4YrwEYxwEYgAQyBwgCEAAYgAQyEAgDEC4YrwEYxwEYyQMYgAQyBwgEEAAYgAQyBggFEEUYPDIGCAYQRRg8MgYIBxBFGDzSAQgyODg3ajBqN6gCALACAA&sourceid=chrome&ie=UTF-8#lrd=0x3ba85a591534d04b:0x2b23d47dd3e90330,3,,,,';

          var targetUrl = DEFAULT_REVIEW_URL; // safe default
          if (res.google_review_url && res.google_review_url.trim() !== '') {
            try {
              var urlObj = new URL(res.google_review_url.trim());
              var isGoogle = /^(www\.)?(maps\.)?google\.(com|co\.\w{2,3}|\w{2,3})$|^g\.page$|^search\.google\.com$/i.test(urlObj.hostname);
              if (urlObj.protocol === 'https:' && isGoogle) {
                targetUrl = res.google_review_url.trim();
              }
            } catch (e) {
              // Invalid URL — fall through to default
            }
          }
          window.location.href = targetUrl;
        }, 2000);
      } else {
        $.toaster({ priority: 'danger', title: 'Error', message: res.message || 'Something went wrong.' });
      }
    },
    error: function() {
      $.toaster({ priority: 'danger', title: 'Error', message: 'Network error. Please try again.' });
    },
    complete: function() {
      $('#feedback_save').prop('disabled', false).html('<i class="fas fa-paper-plane"></i>&nbsp; Submit Feedback');
    }
  });
}

/* ─── Document Ready ───────────────────────────────────────────────────────── */

$(document).ready(function() {

  switch (ctrl_page[1]) {

    case 'customer_feedback':
    case 'lead_form':

      // Init Select2 on static selects
      $('#purchase_occasion, #staff_id').select2({ minimumResultsForSearch: Infinity });
      $('#cus_state, #cus_city').select2();

      // Load dropdowns
      get_country_code();
      loadEmployees();

      // Fetch branch location then load State → City with branch defaults
      var bid = $('#branch_id').val();
      if (bid) {
        $.ajax({
          url: base_url + 'index.php/refer_customer/get_branch_location',
          type: 'POST',
          data: { branch_id: bid },
          dataType: 'json',
          success: function(branch) {
            $('#ed_city').val(branch.id_city || '');
            get_state(branch.id_state || '');
          },
          error: function() {
            get_state(); // fallback — no defaults
          }
        });
      } else {
        get_state(); // no branch — no defaults
      }

      // Staff rating panel toggle
      $('#staff_id').on('change', function() {
        if ($(this).val()) {
          $('#staff_rating_wrap').slideDown(200);
        } else {
          $('#staff_rating_wrap').slideUp(200);
          $('#rating_staff').val(0);
          $('#staff_rating_wrap .star-rating i').removeClass('selected hovered');
        }
      });

      // Marital status → Anniversary toggle
      $('#marital_status').on('change', function() {
        if ($(this).val() === 'Yes') {
          $('#anniversary_wrap').slideDown(200);
        } else {
          $('#anniversary_wrap').slideUp(200);
          $('#cus_doa').val(''); // clear date when hidden
        }
      });

      // State → City chained
      // If #ed_city has a value (set by customer autocomplete or initial branch load),
      // use it as the default city, then clear it so subsequent manual changes don't reuse it.
      $('#cus_state').on('change', function() {
        var stateId    = $(this).val();
        var pendingCity = $('#ed_city').val();
        $('#ed_city').val(''); // consume it — one-time use
        if (stateId) get_city(stateId, pendingCity || '');
      });

      // Mobile keyup → autocomplete search
      $('#cus_mobile').on('keyup', function(e) {
        var val = $(this).val();
        var key = e.keyCode || e.charCode;
        if (key !== 8 && key !== 46 && val.length >= 5) {
          getSearchCustomers(val);
        }
      });

      // Star rating — delegated to handle dynamically shown elements
      $(document).on('mouseenter', '.star-rating i', function() {
        var rating  = parseInt($(this).data('rating'));
        $(this).closest('.star-rating').find('i').each(function() {
          $(this).toggleClass('hovered', parseInt($(this).data('rating')) <= rating);
        });
      });
      $(document).on('mouseleave', '.star-rating', function() {
        $(this).find('i').removeClass('hovered');
      });
      $(document).on('click', '.star-rating i', function() {
        var $wrap  = $(this).closest('.star-rating');
        var target = $wrap.data('target');
        var rating = parseInt($(this).data('rating'));
        $wrap.find('i').each(function() {
          $(this).toggleClass('selected', parseInt($(this).data('rating')) <= rating);
        });
        $(target).val(rating);
      });

      // Submit with validation
      $('#feedback_save').on('click', function() {
        var mobile = $.trim($('#cus_mobile').val());
        var name   = $.trim($('#cus_name').val());
        var email  = $.trim($('#cus_mail').val());
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!mobile) {
          $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please enter Mobile Number..!' });
          $('#cus_mobile').focus(); return;
        }
        if (!name) {
          $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please enter Name..!' });
          $('#cus_name').focus(); return;
        }
        if (email && !emailPattern.test(email)) {
          $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please enter valid Email ID..!' });
          $('#cus_mail').focus(); return;
        }
        if (!$('#cus_pincode').val()) {
          $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please enter Pincode..!' });
          $('#cus_pincode').focus(); return;
        }
        if (!$('#cus_state').val()) {
          $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please enter State..!' });
          return;
        }
        var ambianceRating   = parseInt($('#rating_ambiance').val()) || 0;
        var collectionRating = parseInt($('#rating_collection').val()) || 0;
        if (ambianceRating === 0 || collectionRating === 0) {
          $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please rate both Ambiance and Collection..!' });
          return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>&nbsp; Submitting...');
        customer_feedback_save();
      });

      break;
  }

});
