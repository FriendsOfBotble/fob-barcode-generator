@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.templates.name') }}</h4>
                    <div class="card-actions">
                        <a href="{{ route('barcode-generator.templates.create') }}" class="btn btn-primary">
                            <x-core::icon name="ti ti-plus" />
                            {{ trans('plugins/fob-barcode-generator::barcode-generator.templates.create') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {!! $table->renderTable() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
