/**
 * Created by julienmoulis on 15/03/2017.
 */
(function ($){
})(jQuery);

function actionPhone($wrapper, $addItem, $removeItem, $item){
    $wrapper.on('click', $removeItem, function (e) {
        e.preventDefault();
        $(this).closest($item).remove();
    });

    $wrapper.on('click', $addItem, function (e) {
        e.preventDefault();
        var prototype = $wrapper.data('prototype');
        var index = $wrapper.data('index');
        var newForm = prototype.replace(/__name__/g, index);
        $wrapper.data('index', index + 1);
        $(this).before(newForm);
    });
}