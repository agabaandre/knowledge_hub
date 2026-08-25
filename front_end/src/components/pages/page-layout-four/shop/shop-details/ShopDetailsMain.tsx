"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import productsData from '@/data/products-data';
import { idType, ProductsType } from '@/interFace/interFace';
import Image from 'next/image';
import React from 'react';
import BookDetails from './BookDetails';
import BookReview from './BookReview';
import trustpilotStar from '../../../../../../public/assets/images/shape/trustpilot-star.webp';
import trustpilotLogo from '../../../../../../public/assets/images/shape/trustpilot-logo.webp';
import BestSellersProductSlider from '@/components/sliders/BestSellersProductSlider';
import { useDispatch, useSelector } from 'react-redux';
import { RootState } from '@/redux/store';
import { cart_product, decrease_quantity } from '@/redux/slices/cartSlice';
import { wishlist_product } from '@/redux/slices/wishlistSlice';
import Link from 'next/link';
import BookReviewForm from '@/form/BookReviewForm';

const ShopDetailsMain = ({ id }: idType) => {
    const book = productsData.find((item) => item?.id == id) as ProductsType;
    const dispatch = useDispatch()

    const cartProducts = useSelector(
        (state: RootState) => state.cart.cartProducts
    );
    const myData = cartProducts.find((item: { id: number }) => item.id === book?.id);

    const handleAddToCart = (product: ProductsType) => {
        if (product) {
            dispatch(cart_product(product))
        }
    }
    const handleDecrease = (product: ProductsType) => {
        if (product) {
            dispatch(decrease_quantity(product));
        }
    }
    //handle add to wishlist
    const handleAddToWishlist = (product: ProductsType) => {
        if (product) {
            dispatch(wishlist_product(product))
        }
    }
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Book Details' />
            {/* -- shop details area start -- */}
            <section className="bd-shop-details-area section-space">
                <div className="container">
                    <div className="row gy-30 justify-content-center">
                        <div className="col-xl-3 col-lg-3 col-md-5 col-sm-6 col-8">
                            <div className="bd-product-details-thumb sidebar-sticky">
                                {book?.image && <Image src={book?.image} style={{ width: "100%", height: "auto" }} priority alt="image not found" />}
                            </div>
                        </div>
                        <div className="col-xl-7 col-lg-7 col-md-12">
                            <div className="bd-product-details-wrapper">
                                <h2 className="bd-product-details-title small mb-15">{book?.title}</h2>
                                <h6 className="bd-book-author mb-30"><span>Author:</span> Richard Osman</h6>
                                <div className="bd-book-format mb-30">
                                    <span className="bd-book-format-label">FORMAT</span>
                                    <div className="bd-book-format-wrap">
                                        <div className="bd-book-format-option selected">
                                            <span className="bd-book-format-type">Hardcover</span>
                                            <span className="bd-book-format-price">
                                                {book?.discount && <span className="old-price">{`$${book?.discount}`}</span>}{" "}
                                                <span className="new-price">{`$${book?.price}`}</span>
                                            </span>
                                        </div>
                                        <div className="bd-book-format-option">
                                            <span className="bd-book-format-type">Paperback</span>
                                            <span className="bd-book-format-price">
                                                <span className="old-price">$12.99</span>{" "}
                                                <span className="new-price">$10.99</span>
                                            </span>
                                        </div>
                                        <div className="bd-book-format-option">
                                            <span className="bd-book-format-type">eBook</span>
                                            <span className="new-price">$6.99</span>
                                        </div>
                                    </div>
                                </div>
                                <div className="bd-product-details-stock-quantity mb-30">
                                    <div className="bd-product-details-stock">In Stock</div>
                                    <div className="bd-product-quantity">
                                        <button onClick={() => handleDecrease(book)} className="decrease">
                                            <i className="fa-regular fa-minus"></i>
                                        </button>
                                        <input className="bd-product-quantity-input" type="text" readOnly value={myData?.quantity || 0} />
                                        <button onClick={() => handleAddToCart(book)} className="increase">
                                            <i className="fa-regular fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div className="bd-product-details-action d-flex-items gap-15 mb-30">
                                    <Link href="/cart" className="bd-btn btn-primary">  View Cart</Link>
                                        
                                    <button onClick={() => handleAddToWishlist(book)} className="bd-btn btn-outline-primary"> <span className="left-icon"><i
                                        className="fa-light fa-bookmark"></i></span>Add To Wishlist</button>
                                </div>
                                <div className="bd-trustpilot-review mb-30">
                                    <div className="trustpilot-info">
                                        <div className="rating">
                                            <div className="stars">
                                                <Image src={trustpilotStar} alt="star" />
                                            </div>
                                            <span className="rating-value">4.9/5.0</span>
                                        </div>
                                        <div className="trustpilot">
                                            <div className="trustpilot-logo">
                                                <Image src={trustpilotLogo} alt="logo" />
                                            </div>
                                            <span className="reviews-count">21,000+ Reviews</span>
                                        </div>
                                    </div>
                                    <div className="review-text">
                                        <p>Thousands of readers trust us for offering the best book shopping experience with
                                            top-notch customer support.</p>
                                    </div>
                                </div>
                                <div className="bd-product-details-content mb-30">
                                    <h3 className="bd-product-details-title medium mb-15">Description</h3>
                                    <p className="bd-product-desc">We Solve Murders is a thrilling new novel by Richard Osman,
                                        blending humor, mystery, and unforgettable characters. Follow a group of amateur
                                        sleuths as they uncover secrets and solve crimes with sharp wit and charm.</p>
                                    <h3 className="bd-product-details-title medium mb-15">Book Details</h3>
                                    <BookDetails />
                                </div>
                                <div className="bd-product-about-author mb-30">
                                    <h3 className="bd-product-details-title medium mb-15">About the Author</h3>
                                    <p className="bd-product-desc">Richard Osman is an internationally bestselling author and
                                        television presenter. His novels have captivated readers worldwide, with his
                                        Thursday Murder Club series being turned into a major film production. He lives in
                                        London with his wife, Ingrid, and their cat, Liesl.</p>
                                </div>
                                <div className="bd-product-review">
                                    <h3 className="bd-product-details-title medium mb-15">{`Book's`} Review</h3>
                                    <div className="bd-postbox-comment mb-30">
                                        <BookReview />
                                    </div>
                                    <h3 className="bd-product-details-title medium mb-15">Write a Review</h3>
                                    <div className="bd-review-form">
                                        <div className="bd-review-form-rating mb-15">
                                            <p>Your email address will not be published. Required fields are marked *</p>
                                            <div className="bd-ratings-wrapper bd-ratings-wrapper-two rating-spacing-2">
                                                <i className="fa fa-star"></i>
                                                <i className="fa fa-star"></i>
                                                <i className="fa fa-star"></i>
                                                <i className="fa fa-star"></i>
                                                <i className="fa fa-star"></i>
                                                <input type="hidden" name="rating" defaultValue="5" />
                                            </div>
                                        </div>
                                        <BookReviewForm />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- shop details area end -- */}
            <BestSellersProductSlider />
        </>
    );
};

export default ShopDetailsMain;