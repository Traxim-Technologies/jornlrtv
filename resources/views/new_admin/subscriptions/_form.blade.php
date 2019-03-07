@include('notification.notify')

<div class="row">

    <div class="col-md-10 ">

        <div class="box box-primary">

            <div class="box-header label-primary">

                <b>@yield('title')</b>

                <a href="{{ route('admin.subscriptions.index') }}" style="float:right" class="btn btn-default">{{ tr('view_subscriptions') }}</a>
            </div>

            <form class="form-horizontal" action="{{ Setting::get('admin_delete_control') == YES ? '#' :  route('admin.subscriptions.save') }}" method="POST" enctype="multipart/form-data" role="form">

                <input type="hidden" name="subscription_id" value="{{ $subscription_details->id }}">

                <input type="hidden" name="unique_id" value="{{ $subscription_details->unique_id }}">

                <div class="box-body">

                    <div class="form-group">

                        <div class="col-md-6">
                            <label for="title" class="">{{ tr('title') }}</label>

                            <input type="text" required name="title" class="form-control" id="title" value="{{ $subscription_details->title ? $subscription_details->title : old('title') }}" placeholder="{{ tr('title') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="amount" class="">{{ tr('amount') }}</label>

                            <input type="text" required name="amount" class="form-control" id="amount" placeholder="{{ tr('amount') }}" step="any" value="{{ ($subscription_details->amount) ? $subscription_details->amount : old('amount') }}"  maxlength="5">
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-md-12">                       
                            <label for="plan" class="">{{ tr('plan') }} <br><span class="text-red">
                            <b>{{ tr('plan_note') }}</b></span>
                            </label>

                            <input type="number" min="1" max="12" required name="plan" class="form-control" id="plan" value="{{ ($subscription_details->plan) ? $subscription_details->plan : old('plan') }}" title="{{ tr('month_of_plans') }}" placeholder="{{ tr('plans') }}">
                        </div>
                    </div>

                    <div class="col-md-12">

                        <div class="form-group">
                            <label for="description" class="">{{ tr('description') }}</label>

                            <textarea id="ckeditor" name="description" required class="form-control" placeholder="{{ tr('description') }}">{{ ($subscription_details->description) ? $subscription_details->description : old('description') }}</textarea>
                           
                        </div>
                    </div>

                </div>

                 <div class="box-footer">
                    <a href="" class="btn btn-danger">{{ tr('reset') }}</a>
                    <button type="submit" class="btn btn-success pull-right" @if(Setting::get('admin_delete_control') == YES) disabled @endif>{{ tr('submit') }}</button>
                </div>

            </form>
        
        </div>

    </div>

</div>
