(function($) {
    $.widget("q4.downloads", {
        options: {
            listName: '',
            tagList: [],
            itemTpl: function(inst){},
            afterLoad: function(){},
            tplLogic: {}
        },

        _init: function(){
            this.getDownloadItems();
        },

        dataDto: function(){
            return dataObj = {
                serviceDto: {
                    RevisionNumber: GetRevisionNumber(),
                    LanguageId: GetLanguageId(),
                    Signature: GetSignature(),
                    ViewType: GetViewType(),
                    ViewDate: GetViewDate(),
                    StartIndex: 0,
                    ItemCount: -1,
                    IncludeTags: true,
                    TagList : this.options.tagList
                },
                assetType: this.options.listName,
            };
        },

        getDownloadItems: function(startDate, endDate){
            var _ = this;

            $.ajax({
                type: "POST",
                url: "/Services/ContentAssetService.svc/GetContentAssetList",
                data: JSON.stringify(_.dataDto()),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    data.logic = _.options.tplLogic;
                    _.element.html(Mustache.render(_.options.itemTpl(_), data));
                    if (_.options.afterLoad !== undefined && typeof(_.options.afterLoad) === 'function') {
                        _.options.afterLoad();
                    }
                },
                error: function (){
                   
                }
            });
        },

        destroy: function() {
            this.element.html('');
        },

        _setOption: function(option, value) {
            this._superApply(arguments);
        }
    });
})(jQuery);