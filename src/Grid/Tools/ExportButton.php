<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;

class ExportButton extends AbstractTool
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new Export button instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Set up script for export button.
     */
    protected function setUpScripts()
    {
        $script = <<<JS
$('.{$this->grid->exportSelectedName()}').click(function (e) {
    e.preventDefault();
    
    var rows = LA.grid.selected('{$this->grid->getName()}').join(',');
    if (! rows) {
        return false;
    }
    
    var href = $(this).attr('href').replace('__rows__', rows);
    location.href = href;
});
JS;

        Admin::script($script);
    }

    /**
     * @return string|void
     */
    protected function renderExportAll()
    {
        if (! $this->grid->exporter()->option('show_export_all')) {
            return;
        }
        $all = trans('admin.all');

        return "<li><a href=\"{$this->grid->exportUrl('all')}\" target=\"_blank\">{$all}</a></li>";
    }

    /**
     * @return string
     */
    protected function renderExportCurrentPage()
    {
        if (! $this->grid->exporter()->option('show_export_current_page')) {
            return;
        }

        $page = $this->grid->model()->getCurrentPage() ?: 1;
        $currentPage = trans('admin.current_page');

        return "<li><a href=\"{$this->grid->exportUrl('page', $page)}\" target=\"_blank\">{$currentPage}</a></li>";
    }

    /**
     * @return string|void
     */
    protected function renderExportSelectedRows()
    {
        if (
            ! $this->grid->option('show_row_selector')
            || ! $this->grid->exporter()->option('show_export_selected_rows')
        ) {
            return;
        }

        $selectedRows = trans('admin.selected_rows');

        return "<li><a href=\"{$this->grid->exportUrl('selected', '__rows__')}\" target=\"_blank\" class='{$this->grid->exportSelectedName()}'>{$selectedRows}</a></li>";
    }

    /**
     * Render Export button.
     *
     * @return string
     */
    public function render()
    {
        $this->setUpScripts();

        $export = trans('admin.export');

        return <<<EOT
<div class="btn-group " style="margin-right:3px">
    <button type="button" class="btn btn-sm btn-custom dropdown-toggle" data-toggle="dropdown">
        <span class="hidden-xs">{$export}&nbsp;&nbsp;</span>
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        {$this->renderExportAll()}
        {$this->renderExportCurrentPage()}
        {$this->renderExportSelectedRows()}
    </ul>
</div>
EOT;
    }
}
