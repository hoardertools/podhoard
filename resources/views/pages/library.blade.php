@extends('layouts.default')

@section('title', $library->name)

@section('content')
	<!-- begin page-header -->
	<h1 class="page-header">{{$library->name}}</h1>
	<!-- end page-header -->

	<!-- begin panel -->
	<div class="panel panel-inverse">
		<div class="panel-heading">
			<h4 class="panel-title">Available Podcasts</h4>
			<div class="panel-heading-btn">
				@if(\App\Http\Functions\HasWritePermissions::check())
					<a href="/library/{{$library->slug}}/createPodcast" class="btn btn-xs  btn-success">Add Podcast</a>
				@endif
				@if($view == "table")
					<a href="?view=grid" class="btn btn-xs  btn-primary">Switch to Grid View</a>
				@elseif($view == "grid")
					<a href="?view=table" class="btn btn-xs  btn-primary">Switch to Table View</a>
				@endif
			</div>
		</div>
		<div class="panel-body">
			<div class="row">
				@if(session('success'))
					<div class="alert alert-success alert-dismissible" id="sectionAlert">
						{{ session('success') }}
					</div>
				@endif
				@if($view == "table")

						<table id="data-table-default" class="table table-striped table-bordered align-middle text-nowrap">
							<thead>
							<tr>
								<th>Name</th>
								<th>Episodes</th>
								<th>Latest Addition</th>
								<th>Total Playtime (hours)</th>
							</tr>
							</thead>
							<tbody>
							@foreach($library->podcasts()->orderBy("name", "ASC")->get() as $podcast)
								<tr>
									<td><a href="/library/{{$library->slug}}/podcast/{{$podcast->id}}">{{$podcast->name}}</a></td>
									<td>{{$podcast->total_episodes}}</td>
									<td>{{$podcast->latest_addition_at}}</td>
									<td>{{round($podcast->total_playtime / 60 / 60, 1) }}</td>
								</tr>
							@endforeach
							</tbody>
						</table>

				@elseif($view == "grid")

					@foreach($library->podcasts()->orderBy("name", "ASC")->get() as $podcast)
						<div class="card border-0 col-lg-2">
							<a href="/library/{{$library->slug}}/podcast/{{$podcast->id}}">
							<img class="card-img-top podcastImage" id="{{$podcast->id}}"  alt="">
							<div class="card-body">
								<p class="card-text"><b>{{$podcast->name}}</b><br>Episodes: {{$podcast->episodes()->count()}}</p>
							</div>
							</a>
						</div>
					@endforeach
					@endif
			</div>
		</div>
	</div>
	<!-- end panel -->
@endsection

@push('scripts')
	<link href="/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
	<link href="/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
	<script src="/assets/plugins/datatables.net/js/dataTables.min.js"></script>
	<script src="/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
	<script src="/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
	<script src="/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
	<script>
		$(document).ready(function () {
			// Iterate over each element with the class 'podcastImage'
			$('.podcastImage').each(function () {
				var element = $(this);
				var podcastId = element.attr('id'); // Get the ID attribute of the current element

				// Make sure the element has an ID
				if (podcastId) {
					// Make the AJAX call to get the image URL
					$.ajax({
						url: '/getPodcastImage/' + podcastId,
						method: 'GET',
						success: function (data) {
							// Update the 'src' attribute with the returned data
							element.attr('src', data);
						},
						error: function () {
							console.error('Error fetching data for podcast ID:', podcastId);
						}
					});
				}
			});
		});
		$('#data-table-default').DataTable({
			responsive: true,
			pageLength: 25
		});


	</script>
@endpush