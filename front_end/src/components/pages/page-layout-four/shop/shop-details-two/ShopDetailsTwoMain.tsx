"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import Link from 'next/link';
import React, { useState } from 'react';
import Image from 'next/image';
import DeluxeBuildingBlocksTable from './DeluxeBuildingBlocksTable';
import PostboxComment from './PostboxComment';
import ReviewProgress from './ReviewProgress';
import DetailsInformation from './DetailsInformation';

// Swiper
import { Navigation, Thumbs } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Swiper as SwiperType } from 'swiper';

import paymentCard from "../../../../../../public/assets/images/shape/payment-card.webp";
import productImg from "../../../../../../public/assets/images/shop/product-img-1.webp";
import productImg2 from "../../../../../../public/assets/images/shop/product-img-2.webp";
import productImg3 from "../../../../../../public/assets/images/shop/product-img-3.webp";
import productImg4 from "../../../../../../public/assets/images/shop/product-img-4.webp";
import productImg5 from "../../../../../../public/assets/images/shop/product-img-5.webp";
import ShopTwoReviewForm from '@/form/ShopTwoReviewForm';

const productImages = [
    { id: 1, image: productImg },
    { id: 2, image: productImg2 },
    { id: 3, image: productImg3 },
    { id: 4, image: productImg4 },
    { id: 5, image: productImg5 },
];

const ShopDetailsTwoMain = () => {
    const [thumbsSwiper, setThumbsSwiper] = useState<SwiperType | null>(null);
    const [quantity, setQuantity] = useState(1);

    const increaseQuantity = () => {
        setQuantity(prevQuantity => prevQuantity + 1);
    };

    const decreaseQuantity = () => {
        if (quantity > 1) {
            setQuantity(prevQuantity => prevQuantity - 1);
        }
    };
    return (
        <>
            <Breadcrumbs breadcrumbTitle="Shop Details Standard" />
            {/* -- shop details area start -- */}
            <section className="bd-shop-details-area section-space">
                <div className="container">
                    <div className="row gy-30 justify-content-center">
                        <div className="col-xl-5 col-lg-6">
                            <div className="bd-product-details-thumb-wrap">
                                <div className="bd-product-details-thumb-top mb-30">
                                    <div className='bd-product-details-active p-relative'>
                                        <Swiper
                                            modules={[Navigation, Thumbs]}
                                            spaceBetween={10}
                                            navigation={{
                                                nextEl: ".product-details-button-next",
                                                prevEl: ".product-details-button-prev",
                                            }}
                                            thumbs={{ swiper: thumbsSwiper }}
                                        >
                                            {productImages.map((item) => (
                                                <SwiperSlide key={item.id}>
                                                    <div className="bd-product-details-thumb-2">
                                                        <Image
                                                            style={{ width: "100%", height: "auto" }}
                                                            src={item.image}
                                                            alt={`product image ${item.id}`}
                                                        />
                                                    </div>
                                                </SwiperSlide>
                                            ))}
                                        </Swiper>
                                        <div className="bd-product-details-nav-button">
                                            <div className="product-details-button-prev"><i className="fa-solid fa-arrow-left"></i>
                                            </div>
                                            <div className="product-details-button-next"><i className="fa-solid fa-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div className="bd-product-details-thumb-bottom">
                                    <div className="bd-product-details-slider-dot">
                                        <div className="swiper bd-product-details-nav">
                                            <Swiper
                                                onSwiper={setThumbsSwiper}
                                                spaceBetween={10}
                                                slidesPerView={4}
                                                modules={[Thumbs]}
                                            >
                                                {productImages.map((item) => (
                                                    <SwiperSlide key={item.id}>
                                                        <button className="custom-button">
                                                            <Image
                                                                style={{ width: "100%", height: "auto" }}
                                                                src={item.image}
                                                                alt={`product thumbnail ${item.id}`}
                                                            />
                                                        </button>
                                                    </SwiperSlide>
                                                ))}
                                            </Swiper>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-7 col-lg-6">
                            <div className="bd-product-details-wrapper">
                                <div className="d-flex-items gap-15 mb-15">
                                    <div className="bd-product-details-stock">In Stock</div>
                                    <div className="bd-course-rating-wrap d-flex align-items-center gap-10">
                                        <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                            <i className="fa-solid fa-star"></i>
                                            <i className="fa-solid fa-star"></i>
                                            <i className="fa-solid fa-star"></i>
                                            <i className="fa-solid fa-star"></i>
                                            <i className="fa-solid fa-star"></i>
                                        </div>
                                        <div className="bd-course-rating-text">
                                            <span>4.9 (320 Reviews)</span>
                                        </div>
                                    </div>
                                </div>
                                <h2 className="bd-product-details-title small mb-15">Deluxe Building Blocks Set</h2>
                                <p className="bd-product-details-short-info">
                                    Encourage creativity and learning with this Deluxe Building Blocks Set. Designed for children aged 3 and up, this set provides endless opportunities for imaginative play and skill development.
                                </p>
                                <div className="bd-product-details-price mb-30">
                                    <h6 className="current-price">$59.99</h6>
                                    <h6 className="old-price">$79.99</h6>
                                </div>
                                <div className="product-details-count-wrap d-flex flex-wrap gap-10 align-items-center mb-30">
                                    <div className="bd-product-quantity">
                                        <span className="decrease" onClick={decreaseQuantity}>
                                            <i className="fa-regular fa-minus"></i>
                                        </span>
                                        <input
                                            className="bd-product-quantity-input"
                                            type="text"
                                            value={quantity}
                                            readOnly
                                        />
                                        <span className="increase" onClick={increaseQuantity}>
                                            <i className="fa-regular fa-plus"></i>
                                        </span>
                                    </div>
                                    <div className="product-details-action d-flex flex-wrap align-items-center ml-15">
                                        <Link className="bd-btn btn-outline-border-primary h-40px" href="/cart">
                                            Add To Cart
                                        </Link>
                                    </div>
                                </div>
                                <div className="bd-product-details-action-meta mb-30">
                                    <Link className="text-link" href="#">
                                        <i className="fa-light fa-rotate-reverse"></i> <span>Compare</span>
                                    </Link>
                                    <Link className="text-link" href="/wishlist">
                                        <i className="far fa-heart"></i> <span>Add to Wishlist</span>
                                    </Link>
                                    <Link className="text-link" href="#">
                                        <i className="fa-light fa-circle-exclamation"></i> <span>Ask a question</span>
                                    </Link>
                                </div>
                                <div className="bd-product-details-categories mb-30">
                                    <div className="widget_tag_cloud d-flex flex-wrap align-items-center gap-10">
                                        <p>Categories:</p>
                                        <div className="tagcloud">
                                            <Link href="/shop">Toys</Link>
                                            <Link href="/shop">Building Sets</Link>
                                        </div>
                                    </div>
                                </div>
                                <div className="bd-product-details-share d-flex flex-wrap gap-10 align-items-center mb-25">
                                    <p>Share:</p>
                                    <div className="theme-social text-center">
                                        <ul className="social-icon-list">
                                            <li><Link className="facebook" href="#"><i className="fa-brands fa-facebook-f"></i></Link></li>
                                            <li><Link className="twitter" href="#"><i className="fa-brands fa-x-twitter"></i></Link></li>
                                            <li><Link className="linkedin" href="#"><i className="fa-brands fa-linkedin-in"></i></Link></li>
                                            <li><Link className="instagram" href="#"><i className="fa-brands fa-instagram"></i></Link></li>
                                        </ul>
                                    </div>
                                </div>
                                <div className="bd-product-details-checkout-card">
                                    <p>Guarantee safe and secure checkout</p>
                                    <div className="checkout-card">
                                        <Link href="#"><Image src={paymentCard} alt="Payment Image" /></Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="bd-product-information section-space-top">
                        <div className="row gy-30 justify-content-center">
                            <div className="col-xxl-12 col-xl-12 col-lg-12">
                                <div className="tab-style-two">
                                    <ul className="nav nav-pills mb-35 flex-wrap gap-10" id="pills-tab" role="tablist">
                                        <li className="nav-item" role="presentation">
                                            <button className="nav-link active" id="pills-information-tab" data-bs-toggle="pill" data-bs-target="#pills-information" type="button" role="tab" aria-controls="pills-information" aria-selected="true">Product
                                                Information</button>
                                        </li>
                                        <li className="nav-item" role="presentation">
                                            <button className="nav-link" id="pills-review-tab" data-bs-toggle="pill" data-bs-target="#pills-review" type="button" role="tab" aria-controls="pills-review" aria-selected="false">Review</button>
                                        </li>
                                        <li className="nav-item" role="presentation">
                                            <button className="nav-link" id="pills-specifications-tab" data-bs-toggle="pill" data-bs-target="#pills-specifications" type="button" role="tab" aria-controls="pills-specifications" aria-selected="false">Specifications</button>
                                        </li>
                                    </ul>
                                    <div className="tab-content" id="pills-tabContent">
                                        <div className="tab-pane fade show active" id="pills-information" role="tabpanel" aria-labelledby="pills-information-tab" tabIndex={0}>
                                            <DetailsInformation />
                                        </div>
                                        <div className="tab-pane fade" id="pills-review" role="tabpanel" aria-labelledby="pills-review-tab" tabIndex={0}>
                                            <div className="bd-review-rating-wrapper mb-30">
                                                <ReviewProgress />
                                            </div>
                                            <div className="bd-postbox-comment">
                                                <PostboxComment />
                                            </div>
                                            <div className="bd-review-form">
                                                <div className="bd-review-form-rating mb-15">
                                                    <h4 className="mb-15">Leave a Review</h4>
                                                    <p>Your email address will not be published. Required fields are marked
                                                        *</p>
                                                </div>
                                                <ShopTwoReviewForm />
                                            </div>
                                        </div>
                                        <div className="tab-pane fade" id="pills-specifications" role="tabpanel" aria-labelledby="pills-specifications-tab" tabIndex={0}>
                                            <h5 className="mb-20">Deluxe Building Blocks Set Specifications</h5>
                                            <DeluxeBuildingBlocksTable />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- shop details area end -- */}
        </>
    );
};

export default ShopDetailsTwoMain;
