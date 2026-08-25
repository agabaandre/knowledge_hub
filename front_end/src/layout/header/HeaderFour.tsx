"use client"
import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import LogoImg from '../../../public/assets/images/logo/logo.svg';
import SidebarCart from '@/components/common/sidebar-cart/SidebarCart';
import SidebarMenu from '../sidebar/SidebarMenu';
import CommonHeaderMainMenu from './component/MainMenu';
import useGlobalContext from '@/hooks/useContexts';
import useCart from '@/hooks/useCart';
import SelerAccountForm from '@/form/SelerAccountForm';

const HeaderFour = () => {
    const { scrollDirection, toggleSidebarMenu } = useGlobalContext();
    const [openCart,setOpenCart] = useState(false)
      //cart quantity
      const { getCartProductQuantity } = useCart();
      const TotalCartQuantity = getCartProductQuantity();

    return (
        <>
            {/* -- Header area start -- */}
            <header>
                <div className="container">
                    <div className="bd-header-shop-middle">
                        <div className="row gy-10 align-items-center justify-content-between">
                            <div className="col-xl-2 col-lg-2 col-md-6 col-sm-4 col-5 order-lg-0 order-0">
                                <div className="bd-header-shop-logo">
                                    <Link href="/"><Image style={{ width: "auto", height: "auto" }} src={LogoImg} alt="logo" /></Link>
                                </div>
                            </div>
                            <div className="col-xl-7 col-lg-6 col-md-12 col-sm-12 order-lg-1 order-2">
                                <div className="bd-header-shop-search">
                                    <form action="#">
                                        <input type="text" placeholder="Search by Books" />
                                        <button type="submit"><i className="fa-solid fa-magnifying-glass"></i></button>
                                    </form>
                                </div>
                            </div>
                            <div className="col-xl-3 col-lg-4 col-md-6 col-sm-8 col-5 order-lg-2 order-1">
                                <div className="bd-header-shop-meta d-flex-center gap-30 justify-content-end">
                                    <div className="bd-seller-btn d-none d-sm-block">
                                        <Link href="/contact-us"><i className="fa-regular fa-box-open"></i>
                                            Become A Seller</Link>
                                    </div>
                                    <div className="bd-header-meta">
                                        <div className="bd-user-dropdown">
                                            <button className="meta-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i className="fa-solid fa-user"></i></button>
                                            <div className="dropdown-menu">
                                                <div className="bd-dropdown-login-form">
                                                    <div className="bd-dropdown-login-head">
                                                        <h6 className="title">Sign in</h6>
                                                        <span className="new-link underline">
                                                            <Link href="/sign-up">Create an Account</Link></span>
                                                    </div>
                                                    <SelerAccountForm />
                                                </div>
                                            </div>
                                        </div>
                                        <button onClick={()=> setOpenCart(true)} className="cartmini-open-btn meta-icon" type="button"><i
                                            className="fa-regular fa-cart-shopping"></i>
                                            <span className="item-number">{TotalCartQuantity}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className={`bd-header-area header-style-four ${scrollDirection === "down" ? "bd-sticky" : ""}`}>
                    <div className="container">
                        <div className="bd-header-inner">
                            <div className="bd-header-left">
                                <div className="bd-header-logo">
                                    <Link href="/"><Image src={LogoImg} style={{ width: "100%", height: "auto" }} alt="logo" /></Link>
                                </div>
                                <div className="bd-header-category">
                                    <div className="bd-category-btn">
                                        <i className="fa-solid fa-grid"></i> Category
                                    </div>
                                    <div className="bd-category-dropdown">
                                        <nav>
                                            <ul>
                                                <li><Link href="/shop">Fiction</Link></li>
                                                <li><Link href="/shop">Non-Fiction</Link></li>
                                                <li><Link href="/shop">Science & Technology</Link></li>
                                                <li><Link href="/shop">Biographies</Link></li>
                                                <li><Link href="/shop">{`Children's`} Books</Link></li>
                                                <li><Link href="/shop">Self-Help</Link></li>
                                                <li><Link href="/shop">History</Link></li>
                                                <li><Link href="/shop">Romance</Link></li>
                                            </ul>
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
                                <div className="bd-support-meta">
                                    <div className="icon"><Link href="tel:1234567890"><i className="fa-regular fa-phone-volume"></i></Link>
                                    </div>
                                    <div className="bd-support-content">
                                        <Link href="tel:1234567890">
                                            <h6 className="title">+ 012-3456-7890</h6>
                                        </Link>
                                        <p className="description">24/7 Support Center</p>
                                    </div>
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
               {/* sidebar cart start */}
               <SidebarCart openCart={openCart} setOpenCart={setOpenCart} />
            {/* sidebar cart end */}
             {/* sidebar mobile menu */}
             <SidebarMenu />
        </>
    );
};

export default HeaderFour;