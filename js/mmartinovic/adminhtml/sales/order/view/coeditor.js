var coeditor = Object.extend({
    formId: 'sales-order-view-coeditor-form',
    itemPrefix: 'order_item_',
    // Open a popup dialog
    openPopup: function(url, title, id) {
        if ($('coeditor') && typeof(Windows) != 'undefined') {
             Windows.focus('browser_window');
             return;
         }
         Dialog.info(null, {
            closable:true,
            resizable:false,
            draggable:true,
            className:'magento',
            windowClassName:'popup-window',
            title:title,
            top:50,
            width:600,
            height:400,
            zIndex:1000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:'coeditor'+id,
            url:url,
            onClose:function (param, el) {}
         });
    },
    // Close a popup dialog
    closePopup: function(id) {
        Windows.close('coeditor'+id);
    },
    // Close popup dialog from inside
    popupClosePopup: function(id) {
        window.parent.coeditor.closePopup(id)
    },
    // Replace item text with transport text
    replaceItem: function(id, text) {
        var el = $(window.parent.document.getElementById(coeditor.itemPrefix+id));
        el.firstDescendant().update(text);
    },
    // Handle form submit
    submitForm: function(id) {
        var form = $(coeditor.formId);

        // Trigger varienForm validation
        if (!salesOrderViewCoeditor.validator.validate()) {
            return false;
        }

        new Ajax.Request(form.action, {
            method: form.method,
            parameters: Form.serialize(form),
            asynchronous: true,
            onSuccess: function(transport) {
                coeditor.replaceItem(id, transport.responseText);
                coeditor.popupClosePopup(id);
            },
            onFailure: function() {
                alert('An error occurred. Please try again...');
            }
        });
    }
}, coeditor || {});