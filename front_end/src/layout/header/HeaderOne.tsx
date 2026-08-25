import React from 'react';
import Image from 'next/image';
import headerLogo from "../../../public/assets/images/logo/logo.svg";
import Link from 'next/link';
import SidebarMenu from '../sidebar/SidebarMenu';
import CommonHeaderMainMenu from './component/MainMenu';
import useGlobalContext from '@/hooks/useContexts';

const HeaderOne = () => {
    const { scrollDirection, toggleSidebarMenu } = useGlobalContext();
    return (
        <>
            {/* -- Header area start -- */}
            <header>
                <div className={`bd-header-area header-style-one demo-header ${scrollDirection === "down" ? "bd-sticky" : ""}`} id="header-sticky">
                    <div className="bd-header-inner">
                        <div className="bd-header-left">
                            <div className="bd-header-logo">
                                <Link href="/">
                                    <Image style={{ width: "100%", height: "auto" }} src={headerLogo} alt='logo' />
                                </Link>
                            </div>
                        </div>
                        <div className="bd-header-menu">
                            <nav className="main-menu bd-mobile-menu-active d-none d-xl-block">
                                <CommonHeaderMainMenu />
                            </nav>
                        </div>
                        <div className="bd-header-right">
                            <div className="bd-header-sign-btn">
                                <Link className="bd-btn bd-marquee-btn marquee-text-auto" href="https://themeforest.net/user/topylo" target="_blank">
                                    <span data-text="Purchase Now">
                                        Purchase Now
                                    </span>
                                </Link>
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
            {/* sidebar mobile menu */}
            <SidebarMenu />
        </>
    );
};

export default HeaderOne;