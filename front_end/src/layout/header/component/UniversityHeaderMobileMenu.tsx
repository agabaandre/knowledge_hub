import university_mobile_data from "@/data/header-menu/university-mobile-data";
import useGlobalContext from "@/hooks/useContexts";
import { MenuItem } from "@/interFace/interFace";
import Image from "next/image";
import Link from "next/link";
import React, { useState } from "react";

const UniversityMobileMenu = () => {
    const { toggleSidebarMenu } = useGlobalContext();
    const [activeSubMenu, setActiveSubMenu] = useState<string | null>(null);
    const [activeMegaMenu, setActiveMegaMenu] = useState<string | null>(null);

    const handleActiveSubMenu = (index: string | number) => {
        // Convert index to string for consistent comparisons
        const indexStr = String(index);
        // If clicking the same menu that's already open, close it
        // Otherwise, open the clicked menu and close any other
        setActiveSubMenu(activeSubMenu === indexStr ? null : indexStr);
    };

    const handleActiveMegaMenu = (index: string | number) => {
        // Convert index to string for consistent comparisons
        const indexStr = String(index);
        // If clicking the same mega menu that's already open, close it
        // Otherwise, open the clicked mega menu and close any other
        setActiveMegaMenu(activeMegaMenu === indexStr ? null : indexStr);
    };

    return (
        <>
            <ul>
                {university_mobile_data?.map((item: MenuItem) => (
                    <li
                        key={item.id}
                        className={`${item?.children === true
                            ? "menu-item-has-children"
                            : `${item?.children === false ? "has-mega-menu" : ""}`
                            } ${activeSubMenu === String(item.id) ? "active" : ""}`}
                    >
                        <Link
                            onClick={(e) => {
                                if (item?.hasDropdown === true) {
                                    e.preventDefault();
                                    handleActiveSubMenu(item.id);
                                }
                            }}
                            href={item.link}
                        >
                            {item?.title}
                        </Link>
                        {/* img dropdown */}
                        {item.previewImg === true && (
                            <ul
                                className="mega-menu mega-grid-4"
                                style={{
                                    display: activeSubMenu === String(item.id) ? "block" : "none",
                                }}
                            >
                                {/* Iterate over submenus */}
                                {item?.submenus?.map((subItem, index) => (
                                    <li key={index}>
                                        <Link onClick={toggleSidebarMenu} href={subItem.link} className="home-menu-item">
                                            <div className="home-menu-thumb">
                                                {subItem.previewImg && <Image style={{ width: "100%", height: "auto" }} src={subItem.previewImg} alt="images" />}
                                            </div>
                                            <div className="home-menu-title">{subItem.title}</div>
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        )}

                        {/* dropdown menu */}
                        {item?.hasDropdown === true && item?.submenus?.length && (
                            <ul
                                className="submenu last-children"
                                style={{
                                    display: activeSubMenu === String(item.id) ? "block" : "none",
                                }}
                            >
                                {item?.submenus?.map((dropdownItem, index) => {
                                    // Always create string keys for consistency
                                    const megaMenuKey = `${item.id}-${index}`;
                                    return (
                                        <li
                                            key={index}
                                            className={`menu-item-has-children has-arrow ${activeMegaMenu === megaMenuKey ? "dropdown-opened active" : ""}`}
                                        >
                                            {item?.previewImg === true ? (
                                                <></>
                                            ) : (
                                                <Link 
                                                    href={dropdownItem?.link}
                                                    onClick={(e) => {
                                                        if (dropdownItem?.megaMenu?.length) {
                                                            e.preventDefault();
                                                            handleActiveMegaMenu(megaMenuKey);
                                                        }
                                                    }}
                                                >
                                                    {dropdownItem?.title}
                                                </Link>
                                            )}

                                            {dropdownItem?.megaMenu?.length && (
                                                <ul
                                                    className="submenu"
                                                    style={{
                                                        display: activeMegaMenu === megaMenuKey ? "block" : "none",
                                                    }}
                                                >
                                                    {dropdownItem?.megaMenu?.map(
                                                        (megaMenuItem, megaMenuIndex: number) => (
                                                            <li key={megaMenuIndex}>
                                                                <Link
                                                                    onClick={toggleSidebarMenu}
                                                                    href={megaMenuItem?.link}
                                                                >
                                                                    {megaMenuItem?.title}
                                                                </Link>
                                                            </li>
                                                        )
                                                    )}
                                                </ul>
                                            )}
                                            {dropdownItem?.megaMenu?.length && (
                                                <button 
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        handleActiveMegaMenu(megaMenuKey);
                                                    }}
                                                    className={`bd-menu-close ${activeMegaMenu === megaMenuKey ? "mean-clicked" : ""}`}
                                                >
                                                    <i className="fa fa-chevron-right"></i>
                                                </button>
                                            )}
                                        </li>
                                    );
                                })}
                            </ul>
                        )}
                        {item?.hasDropdown === true && (
                            <button 
                                onClick={(e) => {
                                    e.stopPropagation();
                                    handleActiveSubMenu(item.id);
                                }} 
                                className={`bd-menu-close ${activeSubMenu === String(item.id) ? "mean-clicked" : ""}`}
                            >
                                <i className="fa fa-chevron-right"></i>
                            </button>
                        )}
                    </li>
                ))}
            </ul>
        </>
    );
};

export default UniversityMobileMenu;