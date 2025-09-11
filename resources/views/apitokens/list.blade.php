@extends("layout")
@section("title")
{{ __('all.api_tokens.api_tokens') }} | BigLan
@endsection
@section("content")

@php
	$userPermissions = auth()->user()->permissions();
@endphp

	<div class="row mt-2">
		<div class="col-6">
			{{ csrf_field()}}
			@if(in_array('write-api-tokens', $userPermissions))
   				<a href={{ url('/apitokens/new') }} class="btn btn-sm btn-primary mr-2"><i class="fas fa-plus"></i> {{ __('all.api_tokens.btn_create') }}</a>
            @endif
        </div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				@if(count($tokens) > 0)
   				<table class="table table-striped table-hover" id="tokens">
						<thead class="thead-dark">
							<tr>
            					<th class="text-center">{{ __('all.api_tokens.active') }}</th>
            					<th>{{ __('all.api_tokens.name') }}</th>
            					<th>{{ __('all.api_tokens.token') }}</th>
            					<th>{{ __('all.api_tokens.type') }}</th>
            					<th>{{ __('all.api_tokens.id') }}</th>
            					<th class="text-center">{{ __('all.api_tokens.uses') }}</th>
            					<th class="text-center">{{ __('all.api_tokens.expires') }}</th>
            					<th class="text-center">{{ __('all.api_tokens.last_use') }}</th>
            					<th class="text-center">{{ __('all.api_tokens.actions') }}</th>
            				</tr>
            
						</thead>
						<tbody>
					
   				@foreach($tokens as $token)
							<tr @if(!$token->is_active) class="text-muted"  @endif>
								<td class="text-center">
            						@if($token->is_active)
            							<i class="fas fa-check"></i>
            						@else
            							<i class="fas fa-times"></i>
            						@endif
            					</td>
								<td>
            						{{ $token->name }}
            					</td>
								<td>
            						{{ $token->decrypted_token }}
									@if($token->is_active)
                                    	<a href="javascript:" class="copy-btn" data-text-to-copy="{{ $token->decrypted_token }}" title="{{ __('all.api_tokens.copy') }}"><i class="far fa-clone"></i></a>
                                		<span class="copy-feedback" style="margin-left: 5px; font-size: 0.8em; color: green;"></span>
                                	@endif
            					</td>
                                <td>
            						{{ $token->tokenable_type ?? "-" }}
            					</td>
                                <td>
            						{{ $token->tokenable_id ?? "-" }}
            					</td>
                                <td class="text-center">
                                	@if(isset($token->max_uses))
            							{{ $token->uses_count }} / {!! $token->max_uses ?? "&infin;" !!}
									@else
                                    	&infin;
                                    @endif
            					</td>
                                <td class="text-center">
            						{!! $token->expires_at ?? "&infin;" !!}
            					</td>
                           		<td class="text-center">
            						{{ $token->last_used_at ?? "N/A" }}
            					</td>
                                <td class="text-center">
                                	@if($token->is_active)
            							<form action="{{ route('apitokens.revoke', ['id' => $token->id]) }}" method="POST">
                                			@csrf
                                			<button type="submit" class="btn btn-danger btn-sm">{{ __('all.api_tokens.revoke_btn') }}</button>
                                		</form>
                                	@endif
            					</td>
                                
                         	</tr>
			    @endforeach
						</tbody>
					</table>
            		
            	@else
                	<p>{{ __('all.api_tokens.token_not_found') }}</p>
				@endif
			</div>
		</div>
	</div>
    
	

@endsection
@section('inject-footer')
        <link rel="stylesheet" type="text/css" href={{ url("css/jquery.dataTables.min.css") }}>
        <script type="text/javascript" src={{ url("js/jquery.dataTables.min.js") }}></script>
        <script type="text/javascript">
                                
        async function copyTextToClipboard(text) {
        	try {
            	await navigator.clipboard.writeText(text);
            	return true;
        	} catch (err) {
            	return false;
        	}
    	}

		function showFeedback(buttonElement, message, color = 'green') {
        	const $feedbackSpan = $(buttonElement).next('.copy-feedback');
        	if ($feedbackSpan.length) {
            	$feedbackSpan.text(message);
             	$feedbackSpan.css('color', color);
             	setTimeout(() => {
                	$feedbackSpan.text('');
             	}, 2000);
        	}
    	}
                                
        $(function() {
    		$('#tokens').DataTable({
            	"pageLength": 100
            });
            
        	$('.copy-btn').on('click', async function(event) {
            	event.preventDefault();

            	const $button = $(this);
            	const textToCopy = $button.data('text-to-copy');
				
            	if (textToCopy) {
                
                	const success = await copyTextToClipboard(textToCopy);

                	if (success) {
                    	showFeedback($button, "{{ __('all.api_tokens.copied') }}", 'green');
                	} else {
                    	showFeedback($button, "{{ __('all.api_tokens.failed') }}", 'red');
                	}

            	}
        	
            });
            
		});
	</script>                           	                              
@endsection