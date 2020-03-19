/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

$(document).ready(function() {

  $.ajax({
    url : '/tasks',
    type : 'GET',
    dataType:'JSON',
    success : function(data) {
      for (let i=0; i<data.length; i++) {
        addTask(data[i]);
      }
    },
  });

  function addTask(task) {
    let checked = (task.done == true) ? 'checked' : '';
    let listItem = $('<li id="' + task.id + '" class="list-group-item d-flex justify-content-between lh-condensed task">' +
      '<div><h6 class="title">' + task.title + '</h6><small class="text-muted description">' + task.description + '</small></div>' +
      '<span class="input-group-addon"><input class="done" type="checkbox" ' + checked + '></span></li>');

    if (task.done === true) {
      $('.done-task').append(listItem.clone());
      $('#done-counter').text(parseInt($('#done-counter').text()) + 1);
    } else {
      $('.todo-task').append(listItem.clone());
      $('#todo-counter').text(parseInt($('#todo-counter').text()) + 1);
    }

    listItem.remove();
  }

  $('ul').on('change', 'input.done', function() {
    let listItem = $(this).parents('li');

    $.ajax({
      type: "PUT",
      dataType:'JSON',
      contentType: "application/json; charset=utf-8",
      url: "/task/" + listItem.attr('id'),
      data: JSON.stringify({
        'title': listItem.find('.title').text(),
        'description': listItem.find('.description').text(),
        'done': listItem.find('.done').is(':checked')
      }),
      success: function(data) {
        if ($(this).parents('.todo-task').length) {
          $('#todo-counter').text(parseInt($('#todo-counter').text()) - 1);
        } else {
          $('#done-counter').text(parseInt($('#done-counter').text()) - 1);
        }
        listItem.remove();
        addTask(data);
      },
    });
  });

  $('#taskForm').submit(function(e) {
    e.preventDefault();
    $("#taskForm").find("input textarea").attr('disabled','disabled');

    $.ajax({
      type: "POST",
      dataType:'JSON',
      contentType: "application/json; charset=utf-8",
      url: "/task",
      data: JSON.stringify({
        'title': $('#title').val(),
        'description': $('#description').val(),
        'done': $('#done').is(':checked')
      }),
      success: function(data) {
        addTask(data);
        $('#taskModal').modal('hide');
        $("#taskForm")[0].reset();
      },
      complete: function() {
        $("#taskForm").find("input, textarea").removeAttr('disabled');
      }
    });
  });

});