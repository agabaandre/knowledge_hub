"use client"
import Link from 'next/link';
import React, { useState } from 'react';
import headerLogo from "../../../public/assets/images/logo/logo.svg";
import Image from 'next/image';
import HeaderSearch from './component/HeaderSearch';
import SidebarCart from '@/components/common/sidebar-cart/SidebarCart';
import UniversityHeaderMenu from './component/UniversityHeaderMenu';
import UniversitySidebar from '../sidebar/UniversitySidebar';
import useGlobalContext from '@/hooks/useContexts';
import useCart from '@/hooks/useCart';

const HeaderThree = () => {
    const { toggleSidebarMenu, scrollDirection } = useGlobalContext();
    const [openCart, setOpenCart] = useState(false)
    //cart quantity
    const { getCartProductQuantity } = useCart();
    const TotalCartQuantity = getCartProductQuantity();
    //search functionality
    const [openSearchField, setOpenSearchField] = useState<boolean>(false);
    const handleSearchToggle = () => {
        setOpenSearchField(!openSearchField);
    };

    return (
        <>
            <header>
                <div className="header-style-two">
                    <div className="bd-header-top style-two">
                        <div className="bd-header-top-left">
                            <ul>
                                <li><Link href="tel:+8884467880"><span><i className="fa-solid fa-phone-volume"></i></span>(888)
                                    446-7880</Link></li>
                                <li><Link href="mailto:example@istudy.com"><span><i
                                    className="fa-sharp fa-light fa-envelope"></i></span>example@istudy.com</Link></li>
                            </ul>
                        </div>
                        <div className="bd-header-top-right text-md-end">
                            <Link href="#"><span><i className="fa-sharp fa-regular fa-location-dot"></i></span>Brooklyn, NY 12207, New York</Link>
                        </div>
                    </div>
                    <div className={`bd-header-area ${scrollDirection === "down" ? "bd-sticky" : ""}`} >
                        <div className="bd-header-inner">
                            <div className="bd-header-left">
                                <div className="bd-header-logo logo-white">
                                    <Link href="/"><Image src={headerLogo} style={{ width: "auto", height: "auto" }} priority alt="logo" /></Link>
                                </div>
                                <div className="bd-header-logo logo-black">
                                    <Link href="/"><Image src={headerLogo} style={{ width: "auto", height: "auto" }} priority alt="logo" /></Link>
                                </div>
                            </div>
                            <div className="bd-header-menu">
                                <nav className="main-menu bd-mobile-menu-active d-none d-xl-block">
                                    <UniversityHeaderMenu />
                                </nav>
                            </div>
                            <div className="bd-header-right">
                                <div className="bd-header-meta">
                                    <button onClick={handleSearchToggle} className="bd-search-open-btn meta-icon" type="button"><i
                                        className="fa-regular fa-magnifying-glass"></i></button>
                                    <button onClick={() => setOpenCart(true)} className="cartmini-open-btn meta-icon" type="button"><i
                                        className="fa-regular fa-cart-shopping"></i><span className="item-number">{TotalCartQuantity}</span></button>
                                </div>
                                <div className="bd-header-sign-btn">
                                    <div className="underline"><Link className="bd-btn-text text-primary" href="/sign-in">ERP Login</Link></div>
                                    <Link className="bd-btn btn-outline-border-primary h-40px" href="/apply-online">Apply</Link>
                                </div>
                                <div className="bd-header-hamburger">
                                    <div className="sidebar-toggle">
                                        <Link onClick={toggleSidebarMenu} className="bar-icon" href="#">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            {/* header search input */}
            <HeaderSearch setOpenSearchField={setOpenSearchField} openSearchField={openSearchField} />

            {/* sidebar cart start */}
            <SidebarCart openCart={openCart} setOpenCart={setOpenCart} />
            {/* sidebar cart end */}
            {/* sidebar mobile menu */}
            <UniversitySidebar />
        </>
    );
};

export default HeaderThree;