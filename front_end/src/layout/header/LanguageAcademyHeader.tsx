"use client"
import Link from 'next/link';
import React, { useState } from 'react';
import CategoryDropdown from './component/CategoryDropdown';
import CommonHeaderMainMenu from './component/MainMenu';
import logoImg from "../../../public/assets/images/logo/logo.svg";
import Image from 'next/image';
import SidebarCart from '@/components/common/sidebar-cart/SidebarCart';
import SidebarMenu from '../sidebar/SidebarMenu';
import HeaderSearch from './component/HeaderSearch';
import useGlobalContext from '@/hooks/useContexts';
import useCart from '@/hooks/useCart';

const LanguageAcademyHeader = () => {
    const { scrollDirection, toggleSidebarMenu } = useGlobalContext();
    const [openCart, setOpenCart] = useState(false)
    const [openSearchField, setOpenSearchField] = useState<boolean>(false);
    //cart quantity
    const { getCartProductQuantity } = useCart();
    const TotalCartQuantity = getCartProductQuantity();
    const handleSearchToggle = () => {
        setOpenSearchField(!openSearchField);
    };
    return (
        <>
            {/* -- Header area start -- */}
            <header>
                <div className={`bd-header-area header-style-one ${scrollDirection === "down" ? "bd-sticky" : ""}`}>
                    <div className="bd-header-inner">
                        <div className="bd-header-left">
                            <div className="bd-header-logo">
                                <Link href="/"><Image src={logoImg} alt="logo" /></Link>
                            </div>
                            <div className="bd-header-category d-none d-lg-block">
                                <div className="bd-category-btn">
                                    <i className="fa-solid fa-grid"></i> Category
                                </div>
                                <div className="bd-category-dropdown">
                                    <nav>
                                        <CategoryDropdown />
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div className="bd-header-menu">
                            <nav className="main-menu bd-mobile-menu-active d-none d-xl-block">
                                <CommonHeaderMainMenu />
                            </nav>
                        </div>
                        <div className="bd-header-right">
                            <div className="bd-header-meta">
                                <button onClick={handleSearchToggle} className="bd-search-open-btn meta-icon" type="button"><i className="fa-regular fa-magnifying-glass"></i></button>
                                <button onClick={() => setOpenCart(true)} className="cartmini-open-btn meta-icon" type="button"><i className="fa-regular fa-cart-shopping"></i><span
                                    className="item-number">{TotalCartQuantity}</span></button>
                            </div>
                            <div className="bd-header-sign-btn">
                                <Link className="bd-btn btn-outline-primary h-40px" href="/sign-in">Login</Link>
                                <Link className="bd-btn btn-outline-border-primary h-40px" href="/sign-up">Register</Link>
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
            </header>
            {/* -- Header area end -- */}
            {/* header search input */}
            <HeaderSearch setOpenSearchField={setOpenSearchField} openSearchField={openSearchField} />
            {/* sidebar cart start */}
            <SidebarCart openCart={openCart} setOpenCart={setOpenCart} />
            {/* sidebar cart end */}
            {/* sidebar mobile menu */}
            <SidebarMenu />
        </>
    );
};

export default LanguageAcademyHeader;