import { ProductsType } from '@/interFace/interFace';
import { cart_product } from '@/redux/slices/cartSlice';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import { useDispatch } from 'react-redux';
import GetRating from './GetRating';
interface IShopListProps{
    item:ProductsType
}

const ShopListSingleCard = ({ item }:IShopListProps) => {
    const dispatch = useDispatch()
    const handleAddToCart = (product: ProductsType) => {
        if (product) {
            dispatch(cart_product(product))
        }
    }
    return (
        <>
            <div className="bd-product-list">
                <div className="bd-product-wrapper">
                    <div className="bd-product-thumb text-center">
                        <Link href={`/shop/shop-details/${item.id}`}>{item.image && <Image src={item.image} alt="image" />}</Link>
                    </div>
                </div>
                <div className="bd-product-content">
                    {
                        item.badge &&
                        <div className={`bd-badge ${item.badgeClass} mb-15`}>{item.badge}</div>
                    }
                    <h6 className="bd-product-title underline mb-10">{item.title}</h6>
                    <div className="bd-product-price mb-10">
                        <span className="current-price">{`$${item.price}`}</span>{ " "}
                        {
                            item.discount ? <>
                            <span className="old-price">{`$${item.discount}`}</span>
                            </> : ""
                        }
                        
                    </div>
                    <span className="bd-product-rating fs-14 d-flex rating-color mb-15">
                        {item.rating && <GetRating averageRating={item.rating} />}
                    </span>
                    <p>{item.description}</p>
                    <div className="bd-product-btn">
                        <button onClick={() => handleAddToCart(item)} className="bd-btn btn-primary">Add To Cart</button>
                    </div>
                </div>


            </div>
        </>
    );
};

export default ShopListSingleCard;