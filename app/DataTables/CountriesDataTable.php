<?php

namespace App\DataTables;

use App\Models\Country;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CountriesDataTable extends DataTable
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
            ->editColumn('created_at', function($model){
                return $model->created_at->format('Y-m-d H:i');
            })
            ->editColumn('updated_at', function($model){
                return $model->updated_at->format('Y-m-d H:i');
            })
            ->addColumn('action', 'pages.countries.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Country $model)
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
                    ->setTableId('countries-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
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
            Column::make('name'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::computed('action')->exportable(false)->printable(false)->width(60)->addClass('text-center')->width(150),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Countries_' . date('YmdHis');
    }
}
