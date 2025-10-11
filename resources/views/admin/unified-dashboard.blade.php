@extends('layouts.admin')

@section('title', 'Unified Dashboard | Admin Panel')

@section('page-title', 'Unified Dashboard')

@section('page-description', 'Complete platform overview with all metrics in one place')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p>Complete platform overview with all metrics in one place</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <!-- Date Range Filter -->
                                    <li>
                                        <form method="GET" action="{{ route('admin.unified-dashboard') }}" class="form-inline">
                                            <div class="form-group">
                                                <label class="form-label">From</label>
                                                <div class="form-control-wrap">
                                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                                                </div>
                                            </div>
                                            <div class="form-group mx-2">
                                                <label class="form-label">To</label>
                                                <div class="form-control-wrap">
                                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                                                <a href="{{ route('admin.unified-dashboard') }}" class="btn btn-outline-light btn-sm">Reset</a>
                                            </div>
                                        </form>
                                    </li>
                                    <li><a href="#" class="btn btn-primary" onclick="refreshData()"><em class="icon ni ni-reload"></em><span>Refresh</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Overview Stats Cards -->
            <div class="nk-block">
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-users text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($overview['stats']['total_users']) }}</span>
                                    <span class="sub-title">Authors: {{ number_format($overview['stats']['total_authors']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($overview['stats']['total_revenue'], 2) }}</span>
                                    @if($overview['stats']['revenue_growth'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($overview['stats']['revenue_growth'], 1) }}% this month</span>
                                    @elseif($overview['stats']['revenue_growth'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($overview['stats']['revenue_growth']), 1) }}% this month</span>
                                    @else
                                        <span class="sub-title">No change this month</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Books</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-book text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($overview['stats']['total_books']) }}</span>
                                    <span class="sub-title">Published: {{ number_format($overview['stats']['published_books']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Pending Reviews</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-clock text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($overview['stats']['pending_books']) }}</span>
                                    <span class="sub-title">Need Attention</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Pending Payouts</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-tranx text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($overview['stats']['pending_payouts']) }}</span>
                                    <span class="sub-title">₦{{ number_format($overview['stats']['pending_payout_amount'], 2) }} pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">This Month Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-growth text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($overview['stats']['this_month_revenue'], 2) }}</span>
                                    <span class="sub-title">vs ₦{{ number_format($overview['stats']['last_month_revenue'], 2) }} last month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Payouts</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-check-circle text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($overview['stats']['total_payout_amount'], 2) }}</span>
                                    <span class="sub-title">{{ number_format($overview['stats']['approved_payouts']) }} completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="nk-block">
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Active Users</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-users text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['active_users']) }}</span>
                                    <span class="sub-title">{{ number_format($analytics['new_users']) }} new this period</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Book Views</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-eye text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['book_views']) }}</span>
                                    @if($analytics['views_change'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($analytics['views_change'], 1) }}%</span>
                                    @elseif($analytics['views_change'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($analytics['views_change']), 1) }}%</span>
                                    @else
                                        <span class="sub-title">No change</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Conversion Rate</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-bar-chart text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['conversion_rate'], 2) }}%</span>
                                    <span class="sub-title">Views to purchases</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Author Retention</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-user-check text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($analytics['author_retention'], 1) }}%</span>
                                    <span class="sub-title">Active authors</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Metrics -->
            <div class="nk-block">
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Sales</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-cart text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ number_format($sales['metrics']['total_sales']) }}</span>
                                    @if($sales['metrics']['sales_change'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($sales['metrics']['sales_change'], 1) }}%</span>
                                    @elseif($sales['metrics']['sales_change'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($sales['metrics']['sales_change']), 1) }}%</span>
                                    @else
                                        <span class="sub-title">No change</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Avg Order Value</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-bar-chart text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($sales['metrics']['avg_order_value'], 2) }}</span>
                                    @if($sales['metrics']['aov_change'] > 0)
                                        <span class="sub-title text-success"><em class="icon ni ni-arrow-long-up"></em>{{ number_format($sales['metrics']['aov_change'], 1) }}%</span>
                                    @elseif($sales['metrics']['aov_change'] < 0)
                                        <span class="sub-title text-danger"><em class="icon ni ni-arrow-long-down"></em>{{ number_format(abs($sales['metrics']['aov_change']), 1) }}%</span>
                                    @else
                                        <span class="sub-title">No change</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Platform Commission</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-growth text-warning"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($sales['metrics']['platform_commission'], 2) }}</span>
                                    <span class="sub-title">{{ number_format($sales['metrics']['commission_rate'], 1) }}% rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Gross Revenue</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">₦{{ number_format($analytics['gross_revenue'], 2) }}</span>
                                    <span class="sub-title">Platform: ₦{{ number_format($analytics['platform_revenue'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Revenue Trend Chart -->
                    <div class="col-xxl-8">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Revenue Trend</h6>
                                        <p>Daily revenue over the selected period</p>
                                    </div>
                                    <div class="card-tools">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">Chart Type</a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" onclick="changeChartType('line')"><span>Line Chart</span></a></li>
                                                    <li><a href="#" onclick="changeChartType('bar')"><span>Bar Chart</span></a></li>
                                                    <li><a href="#" onclick="changeChartType('area')"><span>Area Chart</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-chart-canvas">
                                    <canvas id="revenueChart" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Growth Chart -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">User Growth & Activity</h6>
                                        <p>User registration and activity trends</p>
                                    </div>
                                    <div class="card-tools">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">Metrics</a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" onclick="toggleMetric('users')"><span>New Users</span></a></li>
                                                    <li><a href="#" onclick="toggleMetric('authors')"><span>New Authors</span></a></li>
                                                    <li><a href="#" onclick="toggleMetric('books')"><span>Books Published</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-chart-canvas">
                                    <canvas id="userGrowthChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Genre Performance -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Genre Performance</h6>
                                        <p>Sales by book genre</p>
                                    </div>
                                </div>
                                <div class="nk-chart-canvas">
                                    <canvas id="genreChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Authors -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Top Authors</h6>
                                        <p>Most successful authors by revenue</p>
                                    </div>
                                </div>
                                
                                @if(count($topAuthors) > 0)
                                    <div class="nk-tb-list nk-tb-orders">
                                        @foreach($topAuthors as $index => $author)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary">
                                                            <span>{{ $index + 1 }}</span>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="tb-lead">{{ $author->name }}</span>
                                                            <span class="tb-sub">{{ $author->books_count }} books</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col tb-col-end">
                                                    <span class="tb-lead">₦{{ number_format($author->total_earnings, 2) }}</span>
                                                    <span class="tb-sub">Total earnings</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-users" style="font-size: 2rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No author data available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Top Performing Books -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Top Performing Books</h6>
                                        <p>Best sellers in selected period</p>
                                    </div>
                                </div>
                                
                                @if(count($topBooks) > 0)
                                    <div class="nk-tb-list nk-tb-orders">
                                        @foreach($topBooks as $index => $book)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary-dim">
                                                            <span>{{ $index + 1 }}</span>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="tb-lead">{{ $book->title }}</span>
                                                            <span class="tb-sub">{{ $book->user->name }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col tb-col-end">
                                                    <span class="tb-lead">₦{{ number_format($book->total_revenue, 2) }}</span>
                                                    <span class="tb-sub">{{ $book->sales_count }} sales</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-book" style="font-size: 2rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No sales data available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Recent Books Requiring Review -->
                    <div class="col-xxl-6">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Book Submissions</h6>
                                        <p>Latest books submitted for review</p>
                                    </div>
                                </div>
                                @if(count($overview['recent']['books']) > 0)
                                    <div class="nk-tb-list nk-tb-orders">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>Book Title</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Author</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>Status</span></div>
                                            <div class="nk-tb-col"><span>Submitted</span></div>
                                            <div class="nk-tb-col"><span>Action</span></div>
                                        </div>
                                        @foreach($overview['recent']['books'] as $book)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <span class="tb-lead">{{ $book->title }}</span>
                                                    <span class="tb-sub text-primary">{{ $book->genre }}</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span class="tb-sub">{{ $book->user->name }}</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-lg">
                                                    @if($book->status === 'pending')
                                                        <span class="badge badge-dot badge-dot-xs bg-warning">Pending</span>
                                                    @elseif($book->status === 'accepted')
                                                        <span class="badge badge-dot badge-dot-xs bg-success">Published</span>
                                                    @elseif($book->status === 'rejected')
                                                        <span class="badge badge-dot badge-dot-xs bg-danger">Rejected</span>
                                                    @endif
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span class="tb-sub">{{ $book->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    @if($book->status === 'pending')
                                                        <a href="#" class="btn btn-sm btn-primary">Review</a>
                                                    @else
                                                        <a href="#" class="btn btn-sm btn-outline-light">View</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-book" style="font-size: 3rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No recent book submissions</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity Sidebar -->
                    <div class="col-xxl-6">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Activity</h6>
                                        <p>Latest platform activity</p>
                                    </div>
                                </div>
                                
                                @if(count($overview['recent']['users']) > 0 || count($overview['recent']['payouts']) > 0)
                                    <ul class="nk-activity">
                                        @foreach($overview['recent']['users']->take(3) as $user)
                                            <li class="nk-activity-item">
                                                <div class="nk-activity-media user-avatar bg-primary">
                                                    <em class="icon ni ni-user-add"></em>
                                                </div>
                                                <div class="nk-activity-data">
                                                    <div class="label">New user "{{ $user->name }}" registered</div>
                                                    <span class="time">{{ $user->created_at->diffForHumans() }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                        
                                        @foreach($overview['recent']['payouts']->take(2) as $payout)
                                            <li class="nk-activity-item">
                                                <div class="nk-activity-media user-avatar bg-warning">
                                                    <em class="icon ni ni-tranx"></em>
                                                </div>
                                                <div class="nk-activity-data">
                                                    <div class="label">Payout request ₦{{ number_format($payout->amount_requested, 2) }} from {{ $payout->user->name }}</div>
                                                    <span class="time">{{ $payout->created_at->diffForHumans() }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-activity" style="font-size: 2rem; opacity: 0.3;"></em>
                                        <p class="text-soft mt-2">No recent activity</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analytics Tables -->
            <div class="nk-block">
                <div class="row g-gs">
                    <!-- User Engagement -->
                    <div class="col-lg-6">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">User Engagement Metrics</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-primary-dim">
                                                        <em class="icon ni ni-clock"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Avg. Session</p>
                                                    <h4 class="inbox-item-title">{{ $analytics['avg_session_duration'] }}m</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-success-dim">
                                                        <em class="icon ni ni-eye"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Pages/Session</p>
                                                    <h4 class="inbox-item-title">{{ number_format($analytics['pages_per_session'], 1) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-warning-dim">
                                                        <em class="icon ni ni-signin"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Bounce Rate</p>
                                                    <h4 class="inbox-item-title">{{ number_format($analytics['bounce_rate'], 1) }}%</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="statbox">
                                            <div class="inbox-item">
                                                <div class="inbox-item-img">
                                                    <div class="inbox-item-img bg-info-dim">
                                                        <em class="icon ni ni-repeat"></em>
                                                    </div>
                                                </div>
                                                <div class="inbox-item-body">
                                                    <p class="inbox-item-text">Return Rate</p>
                                                    <h4 class="inbox-item-title">{{ number_format($analytics['return_rate'], 1) }}%</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Transactions -->
                    <div class="col-lg-6">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Recent Sales Transactions</h6>
                                    </div>
                                </div>
                                
                                <div class="nk-tb-list nk-tb-ulist">
                                    <div class="nk-tb-item nk-tb-head">
                                        <div class="nk-tb-col"><span class="sub-text">Transaction</span></div>
                                        <div class="nk-tb-col tb-col-mb"><span class="sub-text">Book</span></div>
                                        <div class="nk-tb-col tb-col-md"><span class="sub-text">Amount</span></div>
                                        <div class="nk-tb-col tb-col-lg"><span class="sub-text">Date</span></div>
                                    </div>

                                    @forelse($recentTransactions->take(5) as $transaction)
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col">
                                                <span class="tb-lead">#{{ $transaction->id }}</span>
                                                <span class="tb-sub">{{ $transaction->type }}</span>
                                            </div>
                                            <div class="nk-tb-col tb-col-mb">
                                                <span class="tb-lead">{{ $transaction->book->title ?? 'N/A' }}</span>
                                                <span class="tb-sub">{{ $transaction->book->genre ?? '' }}</span>
                                            </div>
                                            <div class="nk-tb-col tb-col-md">
                                                <span class="tb-lead text-success">₦{{ number_format($transaction->amount, 2) }}</span>
                                            </div>
                                            <div class="nk-tb-col tb-col-lg">
                                                <span>{{ $transaction->created_at->format('M d, Y') }}</span>
                                                <span class="tb-sub">{{ $transaction->created_at->format('g:i A') }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col">
                                                <div class="text-center py-4">
                                                    <em class="icon ni ni-tranx" style="font-size: 3rem; opacity: 0.3;"></em>
                                                    <p class="text-soft mt-2">No transactions found</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
let revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($sales['chartData']['labels']),
        datasets: [{
            label: 'Revenue',
            data: @json($sales['chartData']['revenue']),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₦' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ₦' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
const userGrowthChart = new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: @json($analytics['chartData']['labels']),
        datasets: [
            {
                label: 'New Users',
                data: @json($analytics['chartData']['users']),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            },
            {
                label: 'New Authors',
                data: @json($analytics['chartData']['authors']),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            },
            {
                label: 'Books Published',
                data: @json($analytics['chartData']['books']),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Genre Performance Chart
const genreCtx = document.getElementById('genreChart').getContext('2d');
const genreChart = new Chart(genreCtx, {
    type: 'doughnut',
    data: {
        labels: @json($genreData['labels']),
        datasets: [{
            data: @json($genreData['data']),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#FF6384',
                '#C9CBCF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function toggleMetric(metric) {
    // Toggle dataset visibility
    const datasets = userGrowthChart.data.datasets;
    const dataset = datasets.find(d => d.label.toLowerCase().includes(metric));
    if (dataset) {
        dataset.hidden = !dataset.hidden;
        userGrowthChart.update();
    }
}

function changeChartType(type) {
    revenueChart.config.type = type;
    revenueChart.update();
}

function refreshData() {
    Swal.fire({
        title: 'Refreshing Data...',
        text: 'Please wait while we update the dashboard.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>
@endpush
@endsection