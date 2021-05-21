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
            ->setTableAttribute('class', 'w-100 table-responsive')
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
            Column::make('shipper_name'),
            Column::make('shipper_email'),
            Column::make('shipper_number'),
            Column::make('shipper_country_id'),
            Column::make('shipper_state_id'),
            Column::make('shipper_adress_line'),
            Column::make('shipper_zip_code'),
            Column::make('recipient_name'),
            Column::make('recipient_email'),
            Column::make('recipient_number'),
            Column::make('recipient_country_id'),
            Column::make('recipient_state_id'),
            Column::make('recipient_adress_line'),
            Column::make('recipient_zip_code'),
            Column::make('package_weight'),
            Column::make('package_length'),
            Column::make('package_width'),
            Column::make('package_height'),
            Column::make('package_quantity'),
            Column::make('package_pickup_location'),
            Column::make('package_description'),
            Column::make('package_shipping_date_time'),
            Column::make('package_due_date'),
            Column::make('package_shipment_type'),
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
