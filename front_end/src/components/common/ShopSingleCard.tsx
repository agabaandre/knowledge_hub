"use client"
import { ProductsType } from '@/interFace/interFace';
import { cart_product } from '@/redux/slices/cartSlice';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import { useDispatch } from 'react-redux';
import GetRating from './GetRating';
interface IShopProps {
    item: ProductsType;
}

const ShopSingleCard = ({ item }: IShopProps) => {
    const dispatch = useDispatch()
    const handleAddToCart = (product: ProductsType) => {
        if (product) {
            dispatch(cart_product(product))
        }
    }
    return (
        <>
            <div className="bd-product-card-wrap">
                <div className="bd-product-card">
                    <div className="bd-product-thumb">
                        {item.image && <Image src={item.image} priority alt="book" />}
                    </div>
                    <div className="bd-product-content">
                        <h6 className="bd-product-title underline mb-10">{item.title}</h6>
                        <span className="bd-product-rating fs-14 d-flex justify-content-center rating-color mb-10">
                            {item.rating && <GetRating averageRating={item.rating} />}
                        </span>
                        <div className="bd-product-price">
                            <span className="current-price">{`$${item.price}`}</span>
                        </div>
                    </div>
                    <div className="bd-product-cart-btn">
                        <button onClick={() => handleAddToCart(item)} className="bd-btn btn-primary btn-small" type="button">Add To Cart</button>
                    </div>
                </div>
                <div className="bd-product-details-btn">
                    <Link href={`/shop/shop-details/${item.id}`} className="bd-btn btn-outline-secondary btn-small">
                        View Details
                    </Link>
                </div>
                {
                    item.badge &&
                    <div className="bd-product-badge">
                        <span className="bd-circle-badge danger">15% Off</span>
                    </div>
                }
            </div>
        </>
    );
};

export default ShopSingleCard;