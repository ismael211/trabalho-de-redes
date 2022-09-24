/**
 * DataTables Basic
 */

 $(function () {
    'use strict';
  
    var dt_basic_table = $('.datatables-basic'),
      assetPath = '../../../app-assets/';
  
    if ($('body').attr('data-framework') === 'laravel') {
      assetPath = $('body').attr('data-asset-path');
    }
  
    // DataTable with buttons
    // --------------------------------------------------------------------
  
    if (dt_basic_table.length) {
      var dt_basic = dt_basic_table.DataTable({
        ajax: assetPath + 'data/table-datatable.json',
        columns: [
          { data: 'responsive_id' },
          { data: 'id' },
          { data: 'id' }, // used for sorting so will hide this column
          { data: 'full_name' },
          { data: 'email' },
          { data: 'start_date' },
          { data: 'salary' },
          { data: '' },
        ],
        columnDefs: [
          {
            // For Responsive
            className: 'control',
            orderable: false,
            responsivePriority: 2,
            targets: 0
          },
          {
            targets: 2,
            visible: false
          },
          {
            // Avatar image/badge, Name and post
            targets: 3,
            responsivePriority: 4,
            render: function (data, type, full) {
              var $user_img = full['avatar'],
                $name = full['full_name'],
                $post = full['post'];
              if ($user_img) {
                // For Avatar image
                var $output =
                  '<img src="' + assetPath + 'images/avatars/' + $user_img + '" alt="Avatar" width="32" height="32">';
              } else {
                // For Avatar badge
                var stateNum = full['status'];
                var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                var $state = states[stateNum],
                  $name = full['full_name'],
                  $initials = $name.match(/\b\w/g) || [];
                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                $output = '<span class="avatar-content">' + $initials + '</span>';
              }
  
              var colorClass = $user_img === '' ? ' bg-light-' + $state + ' ' : '';
              // Creates full output for row
              var $row_output =
                '<div class="d-flex justify-content-left align-items-center">' +
                '<div class="avatar ' +
                colorClass +
                ' mr-1">' +
                $output +
                '</div>' +
                '<div class="d-flex flex-column">' +
                '<span class="emp_name text-truncate font-weight-bold">' +
                $name +
                '</span>' +
                '<small class="emp_post text-truncate text-muted">' +
                $post +
                '</small>' +
                '</div>' +
                '</div>';
              return $row_output;
            }
          },
          {
            responsivePriority: 1,
            targets: 4
          },
          {
            // Label
            targets: -2,
            render: function (data, type, full) {
              var $status_number = full['status'];
              var $status = {
                1: { title: 'Current', class: 'badge-light-primary' },
                2: { title: 'Professional', class: ' badge-light-success' },
                3: { title: 'Rejected', class: ' badge-light-danger' },
                4: { title: 'Resigned', class: ' badge-light-warning' },
                5: { title: 'Applied', class: ' badge-light-info' }
              };
              if (typeof $status[$status_number] === 'undefined') {
                return data;
              }
              return (
                '<span class="badge badge-pill ' +
                $status[$status_number].class +
                '">' +
                $status[$status_number].title +
                '</span>'
              );
            }
          },
        
        ],

        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                return 'Details of ' + data['full_name'];
              }
            }),
            type: 'column',
            renderer: function (columns) {
              var data = $.map(columns, function (col, i) {
                console.log(columns);
                return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                  ? '<tr data-dt-row="' +
                      col.rowIndex +
                      '" data-dt-column="' +
                      col.columnIndex +
                      '">' +
                      '<td>' +
                      col.data +
                      '</td>' +
                    '</tr>'
                  : '';
              }).join('');
  
              return data ? $('<table class="table"/>').append(data) : false;
            }
          }
        },
        language: {
          paginate: {
            // remove previous & next text from pagination
            previouuus: '&nbssedp;',
            nexddt: '&nafdsfasdfbsp;'
          }
        }
      });
      $('div.head-label').html('<h6 class="mb-0">DataTable with Buttons</h6>');
    }
  
 
  });
  