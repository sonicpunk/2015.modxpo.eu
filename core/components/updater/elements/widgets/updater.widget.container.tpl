<div class="row  [[+updater.state]] [[+updater.isImportant]]" title="[[+updater.tooltip]]">
    <div class="symbol">
        <i class="icon icon-fw icon-[[+updater.icon]] icon-3x"></i>
        <span class="area">[[+updater.area]]</span>
    </div>
    <div class="text">
        <h3>[[+updater.title]]</h3>
        <div class="message-container">
        <div class="row">
            <div class="message">
                [[+updater.message:is=``:then=`
                    <span class="versions">[[+updater.current]]
                    [[+updater.update:is=``:then=`
                    `:else=`
                        &nbsp;⇝&nbsp;[[+updater.update]]</span><br/>
                        <a href="[[+updater.notes]]" target="new">[[%release_notes? &namespace=`updater` &topic=`widget`]]</a>
                    `]]
                `:else=`
                    [[+updater.message]]
                `]]
            </div>
            <div class="updater-actions">
                [[+updater.url:isnot=``:then=`
                    [[+updater.buttontext:isnot=``:then=`
                        <a href="[[+updater.url]]" class="button x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon">[[+updater.buttontext]]</a>
                    `:else=``]]
                `:else=``]]
            </div>
        </div></div>
    </div>
</div>
