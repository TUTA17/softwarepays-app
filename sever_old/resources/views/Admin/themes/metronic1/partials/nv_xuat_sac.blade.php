
<?php
$data = json_decode(@$settings['nv_xuat_sac']); 
	// dd($data);
?>
@if(\Auth::guard('admin')->check() && (in_array(\Auth::guard('admin')->user()->room_id, [1, 2, 3, 4, 5]) || \Auth::guard('admin')->user()->super_admin == 1) && is_array($data))
    <!-- phòng kinh doanh -->
    <style type="text/css">
        .team-member {
            width: 268px;
            float: left;
        }

        .row .col img {
            margin-bottom: 15px;
            max-width: 100%;
            height: auto;
            float: left;
        }
        .team-sale .team-member img {
            border-radius: 50%;
            width: 56px;
            height: 56px;
            border: solid 2px #d19109;
            float: left;
        }
        .team-sale .team-member h4 {
            font-size: 16px;
            letter-spacing: 0px;
            font-weight: 700;
            margin: 0;
        }
        .team-sale .position {

            color: #163c58;
            text-transform: uppercase;
            font-size: 10px;
        }
        .team-member .highlight {
            background: rgb(79,185,232);
            background: linear-gradient(270deg, rgba(79,185,232,0) -10%, rgba(79,185,232,1) 100%);
            font-size: 11px;
            letter-spacing: .5px;
            font-weight: bold;
            padding-left: 5px;
            color: #fff;
        }
        .member-content {
            width: 200px;
            display: inline-block;
            padding-left: 5px;
        }
        .mobile-only{
            display:none;
        }
        /* Mobile responsive */
        @media (max-width: 768px) {
            .page-title, .top-sale-label{
                display:none;
            }
            .desktop-only {
                display: none !important;
            }
            .mobile-only {
                display: block !important;
            }
            .team-member-mobile {
                display: block;
                margin: 15px auto;
                padding: 0 15px;
                width: fit-content;
                max-width: 100%;        /* tránh tràn màn hình */
                text-align: left;       /* text vẫn bám trái */
                box-sizing: border-box;
            }
            .team-member-mobile .label-text {
                font-size: 18px;
                line-height: 23px;
                color: red;
                font-weight: bold;
                margin-right: 8px;
                font-family: fantasy;
            }

            .team-member-mobile .info-text {
                font-size: 16px;
                font-weight: 600;
            }






        }

    </style>
    <label class="top-sale-label desktop-only" style="    font-size: 25px;
    max-width: 205px;
    line-height: 23px;
    color: red;
    font-weight: bold;
    margin-top: 15px;
    margin-left: 0;
    margin-right: 28px;
    font-family: fantasy;">Top sale tháng {{ date('m') }}</label>

    <div class="team-sale nv-xuat-sac" style="    margin-left: -67px;">
       @foreach($data as $k => $v)
            {{-- Hiển thị đầy đủ cho desktop --}}
           <div class="team-member desktop-only" data-style="meta_below">
                 <img alt="" src="{{ asset(@$v->image, 100, null) }}" title="{{ @$v->name }}">
                 <div class="member-content">
                    <h4 class="light">{{ @$v->name }}</h4>
                    <div class="position">{{ @$v->phong }}</div>
                    <p class="highlight">Top {{ $k + 1 }}: {{ number_format(round($v->doanh_so/1000000), 0, '.', '.') }} triệu</p>
                </div>
            </div>
            {{-- Hiển thị đầy đủ cho mobile --}}
            <div class="team-member-mobile mobile-only">
                <span class="label-text">Top sale:</span>
                <span class="info-text light">{{ @$v->name }} - {{ number_format(round($v->doanh_so/1000000), 0, '.', '.') }} triệu</span>
            </div>
        @endforeach
    </div>
@endif
