@extends('client1')




@section('content')

    <section class="wrapper-bottom-sec">
        <div class="p-30">
            <h2 class="page-title">{{language_data('Create New Ticket',Auth::guard('client')->user()->lan_id)}}</h2>
        </div>
        <div class="p-30 p-t-none p-b-none">

            @include('notification.notify')
            <div class="row">

                <div class="col-lg-7">
                    <div class="panel">

                        <div class="panel-heading">
                            <h3 class="panel-title">{{language_data('Create New Ticket',Auth::guard('client')->user()->lan_id)}}</h3>
                        </div>

                        <div class="panel-body">
                            <form method="POST" action="{{ url('user/tickets/post-ticket1') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="form-group">
                                    <label for="subject">{{language_data('Subject',Auth::guard('client')->user()->lan_id)}}</label>
                                    <input type="text" class="form-control" id="subject" name="subject">
                                </div>

                                <div class="form-group">
                                    <label for="message">{{language_data('Message',Auth::guard('client')->user()->lan_id)}}</label>
                                    <textarea class="textarea-wysihtml5 form-control" name="message"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="did">{{language_data('Department',Auth::guard('client')->user()->lan_id)}}</label>
                                    <select name="did" class="selectpicker form-control" data-live-search="true">
                                        @foreach($sd as $d)
                                            <option value="{{$d->id}}">{{$d->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" name="add" class="btn btn-success"><i class="fa fa-plus"></i> {{language_data('Create Ticket',Auth::guard('client')->user()->lan_id)}}</button>
                            </form>


                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

@endsection

{{--External Style Section--}}
@section('script')
    {!! Html::script("assets/libs/handlebars/handlebars.runtime.min.js")!!}
    {!! Html::script("assets/js/form-elements-page.js")!!}
    {!! Html::script("assets/libs/wysihtml5x/wysihtml5x-toolbar.min.js")!!}
    {!! Html::script("assets/libs/bootstrap3-wysihtml5-bower/bootstrap3-wysihtml5.min.js")!!}
@endsection
