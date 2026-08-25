"use client"
import Image from 'next/image';
import Link from 'next/link';
import React, { useState } from 'react';
import headerLogo from '../../../public/assets/images/logo/logo-white.svg';
import Logo from '../../../public/assets/images/logo/logo.svg';
import CommonHeaderMainMenu from './component/MainMenu';
import HeaderSearch from './component/HeaderSearch';
import SidebarCart from '@/components/common/sidebar-cart/SidebarCart';
import SidebarMenu from '../sidebar/SidebarMenu';
import useGlobalContext from '@/hooks/useContexts';
import useCart from '@/hooks/useCart';

const QuranLearningHeader = () => {
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
                <div className={`bd-header-area header-style-three ${scrollDirection === "down" ? "bd-sticky" : ""}`} id="header-sticky">
                    <div className="container">
                        <div className="bd-header-inner-two">
                            <div className="bd-header-left">
                                <div className="bd-header-logo">
                                    <Link href="/">
                                        <Image style={{ width: "100%", height: "auto" }} src={headerLogo} alt='logo' />
                                    </Link>
                                </div>
                                <div className="bd-header-logo-sticky">
                                    <Link href="/">
                                        <Image style={{ width: "100%", height: "auto" }} src={Logo} alt='logo' />
                                    </Link>
                                </div>
                            </div>
                            <div className="bd-header-menu">
                                <nav className="main-menu bd-mobile-menu-active d-none d-xl-block">
                                    <CommonHeaderMainMenu />
                                </nav>
                            </div>
                            <div className="bd-header-right justify-content-end">
                                <div className="bd-header-meta">
                                    <button onClick={handleSearchToggle} className="bd-search-open-btn meta-icon" type="button"><i
                                        className="fa-regular fa-magnifying-glass"></i></button>
                                    <button onClick={() => setOpenCart(true)} className="cartmini-open-btn meta-icon" type="button"><i
                                        className="fa-regular fa-cart-shopping"></i><span className="item-number">{TotalCartQuantity}</span></button>
                                </div>
                                <div className="bd-header-sign-btn">
                                    <Link className="bd-btn btn-outline-border-secondary h-40px" href="/courses-list-two">Find Courses</Link>
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

export default QuranLearningHeader;