@push('style')

    <style>
        fieldset, label {
            margin: 0;
            padding: 0;
        }

        /*body {*/
        /*margin: 20px;*/
        /*}*/

        /*h1 {*/
        /*font-size: 1.5em;*/
        /*margin: 10px;*/
        /*}*/

        /****** Style Star Rating Widget *****/

        .rating {
            border: none;
            float: left;
        }

        .rating > input {
            display: none;
        }

        .rating > label:before {
            margin: 5px;
            font-size: 1.25em;
            font-family: FontAwesome;
            display: inline-block;
            content: "\f005";
        }

        .rating > .half:before {
            content: "\f089";
            position: absolute;
        }

        .rating > label {
            color: #ddd;
            float: right;
        }

        /***** CSS Magic to Highlight Stars on Hover *****/

        .rating > input:checked ~ label, /* show gold star when clicked */
        .rating:not(:checked) > label:hover, /* hover current star */
        .rating:not(:checked) > label:hover ~ label {
            color: #ff9900;
        }

        /* hover previous stars in list */

        .rating > input:checked + label:hover, /* hover current star when changing rating */
        .rating > input:checked ~ label:hover,
        .rating > label:hover ~ input:checked ~ label, /* lighten current selection */
        .rating > input:checked ~ label:hover ~ label {
            color: #ff9900;
        }
    </style>
@endpush

<div id="reviews" class="tab-panel">
    <div class="product-comments-block-tab">
        <div class="row">
            {{--            {{ Form::open(['route' => ['review.store'], 'id' => 'reviewForm']) }}--}}
            <form class="ajaxReviewForm">
                <div class="col-md-6">
                    {!! Form::hidden('product_id', $product->id, ['class' => 'form-control product_id']) !!}
                    <div class="form-group">
                        <label for="description">Your review</label>
                        {!! Form::textarea('description', null, ['class' => 'form-control description', 'required', 'id'=>'product_review', 'height'=>'20px', 'placeholder'=> 'Description']) !!}
                    </div>
                    <div class="form-group">
                        <label for="rating">Rating</label><br>
                        <fieldset class="rating">
                            <input type="radio" class="product_rating" id="star5" name="rating" value="5"/>
                            <label class="full" for="star5" title="Awesome - 5 stars"></label>
                            {{--<input type="radio" class=rating id="star4half" name="rating" value="4 and a half"/>--}}
                            {{--<label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>--}}
                            <input type="radio" class="product_rating" id="star4" name="rating" value="4"/>
                            <label class="full" for="star4" title="Pretty good - 4 stars"></label>
                            {{--<input type="radio" class=rating id="star3half" name="rating" value="3 and a half"/>--}}
                            {{--<label class="half" for="star3half" title="Meh - 3.5 stars"></label>--}}
                            <input type="radio" class="product_rating" id="star3" name="rating" value="3"/>
                            <label class="full" for="star3" title="Meh - 3 stars"></label>
                            {{--<input type="radio" class=rating id="star2half" name="rating" value="2 and a half"/>--}}
                            {{--<label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>--}}
                            <input type="radio" class="product_rating" id="star2" name="rating" value="2"/>
                            <label class="full" for="star2" title="Kinda bad - 2 stars"></label>
                            {{--<input type="radio" class=rating id="star1half" name="rating" value="1 and a half"/>--}}
                            {{--<label class="half" for="star1half" title="Meh - 1.5 stars"></label>--}}
                            <input type="radio" class="product_rating" id="star1" name="rating" value="1"/>
                            <label class="full" for="star1" title="Sucks big time - 1 star"></label>
                            {{--<input type="radio" class=rating id="starhalf" name="rating" value="half"/>--}}
                            {{--<label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>--}}
                        </fieldset>
                    </div>
                    <div class="form-group">
                        <button class="button btn-comment ajaxReviewSubmit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
        @foreach($product->reviews->where('status', 1) as $review)
            <div class="comment row">
                <div class="col-sm-3 author">
                    <div class="info-author">
                        <span><strong>{{ $review->user->username }}</strong></span>
                        <em>{{ SM::showDateTime($review->created_at) }}</em>
                    </div>
                    <div class="grade">
                        <span>{{ $review->title }}</span>
                        <span class="reviewRating">
                            @for ($i = 0; $i < 5; ++$i)
                                <i class="fa fa-star{{ $review->rating<=$i?'-o':'' }}" aria-hidden="true"></i>
                            @endfor
                           </span>
                    </div>
                </div>
                <div class="col-sm-9 commnet-dettail">
                    {{ $review->description }}
                </div>
            </div>
        @endforeach
    </div>
</div>
@if (Auth::check())
@else
    @push('script')
        <script type="text/javascript">
            $(document).ready(function () {
                $("#product_review").click(function () {
                    $('.loginModal').modal('show');
                });
            });
        </script>
    @endpush
@endif
 