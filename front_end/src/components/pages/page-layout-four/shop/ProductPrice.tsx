"use client";
import React, { useEffect, useRef } from "react";
import noUiSlider, { API } from "nouislider";

const ProductPrice: React.FC = () => {
    const sliderRef = useRef<HTMLDivElement | null>(null);
    const amountRef = useRef<HTMLInputElement | null>(null);
    const sliderInstance = useRef<API | null>(null);

    useEffect(() => {
        if (sliderRef.current && !sliderInstance.current) {
            sliderInstance.current = noUiSlider.create(sliderRef.current, {
                start: [0, 1100],
                connect: true,
                range: {
                    min: 0,
                    max: 2500
                }
            });

            sliderInstance.current.on("update", (values: (string | number)[]) => {
                if (amountRef.current) {
                    amountRef.current.value = `$${values[0].toString()} - $${values[1].toString()}`;
                }
            });
        }

        return () => {
            if (sliderInstance.current) {
                sliderInstance.current.destroy(); 
                sliderInstance.current = null;
            }
        };
    }, []);

    return (
        <div className="bd-shop-widget widget-price">
            <h5 className="bd-widget-title mb-20">Price Range</h5>
            <div className="sidebar-widget-range">
                <div ref={sliderRef} id="slider-range"></div>
                <div className="price-filter mt-15">
                    <input type="text" ref={amountRef} id="amount" readOnly />
                </div>
            </div>
        </div>
    );
};

export default ProductPrice;
