<?php

namespace App\DataTables;

use App\Models\shipment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class shipmentsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('created_at', function ($model) {
                return $model->created_at->format('Y-m-d H:i');
            })
            ->editColumn('updated_at', function ($model) {
                return $model->updated_at->format('Y-m-d H:i');
            })
            ->addColumn('action', 'pages.shipments.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\shipment $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(shipment $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('shipments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('print'),
                Button::make('reload')
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id'),
            Column::make('tracking_id'),
            Column::make('user_id'),
            Column::make('provider'),
            Column::make('status'),
            Column::make('provider_status'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'shipments_' . date('YmdHis');
    }
}
