// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="btn btn-default">Cliquez pour ajouter un visiteur</a>');
var $newLinkLi = $('<li class="text-center"></li>').append($addTagLink);

$(document).ready(function() {
    // Get the ul that holds the collection of tags
    var $collectionHolder = $('ul.visitors');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find('.visitor').length);

    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see code block below)
        addTagForm($collectionHolder, $newLinkLi);
    });

    $(".form-group:last").addClass("text-center");

});

function addTagForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');
    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);
    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li><div class="visitor text-center"><h4 class="btn disabled btn-block btn-warning text-center">Visiteur numéro ' + (index + 1) + '</h3></div></li>').append(newForm);

    // also add a remove button
    $newFormLi.append('<a href="#" class="remove-tag btn btn-default btn-sm">Suppression<i class="fa fa-trash" aria-hidden="true"></i></a>');

    $newLinkLi.before($newFormLi);

    $(".checkbox").attr("data-toggle", "tooltip").attr("data-placement", "right").addClass("tooltip-custom");

    // handle the removal;
    $('.remove-tag').click(function(e) {
        e.preventDefault();
        $(this).parent().remove();
        $collectionHolder.data('index', $collectionHolder.find('.visitor').length);
        return false;
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
}
