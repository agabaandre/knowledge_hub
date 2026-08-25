import React from 'react';

const CartCouponCodeForm = () => {
    return (
        <>
            <form action="#">
                <div className="bd-cart-coupon-input-box">
                    <label>Coupon Code:</label>
                    <div className="bd-cart-coupon-input d-flex flex-wrap gap-30 align-items-center">
                        <input type="text" placeholder="Enter Coupon Code" />
                        <button type="submit" className="bd-btn btn-primary">Apply Coupon
                        </button>
                    </div>
                </div>
            </form>
        </>
    );
};

export default CartCouponCodeForm;