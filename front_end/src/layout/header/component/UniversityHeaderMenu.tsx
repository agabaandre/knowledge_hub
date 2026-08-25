
import university_menu_data from "@/data/header-menu/university-menu-data";
import Image from "next/image";
import Link from "next/link";
import React from "react";

const UniversityHeaderMenu = () => {
  return (
    <>
      <ul>
        {university_menu_data.map((item) => (
          <li
            key={item.id}
            className={`${item?.children
              ? "menu-item-has-children"
              : item?.children === false
                ? "has-mega-menu"
                : ""
              }`}
          >
            <Link href={item?.link}>{item?.title}</Link>

            {/* Render Mega Menu */}
            {item?.previewImg && Array.isArray(item?.submenus) && item?.submenus.length > 0 && (
              <ul className={`mega-menu home-menu-grid`}>
                {item?.submenus.map((subItem, index) => (
                  <li key={index}>
                    <Link href={subItem?.link} className="home-menu-item">
                      <div className="home-menu-thumb">
                        {subItem?.previewImg && (
                          <Image src={subItem?.previewImg} style={{ width: "100%", height: 'auto' }} alt="images" />
                        )}
                      </div>
                      <div className="home-menu-title">{subItem?.title}</div>
                    </Link>
                  </li>
                ))}
              </ul>
            )}

            {/* Render Dropdown Menu */}
            {item?.hasDropdown === true && Array.isArray(item?.submenus) && item.submenus.length > 0 && (
              <ul
                className={`${item?.megaMenu
                    ? `mega-menu ${item.columnFour ? "mega-grid-4" : "mega-grid-5"}`
                    : "submenu last-children"
                  }`}
              >
                {item?.submenus.map((dropdownItem, index) => (
                  <li key={index} className={`${dropdownItem.pluseIncon == true ? "has-arrow" : ""}`}>
                    <Link href={dropdownItem?.link} className="title">{dropdownItem?.title}</Link>

                    {/* Check for megaMenu presence and ensure it's an array */}
                    {dropdownItem?.megaMenu && dropdownItem?.megaMenu.length > 0 && (
                      <ul>
                        {dropdownItem?.megaMenu.map((megaDropDownItem, megaIndex) => (
                          <li key={megaIndex}>
                            <Link href={megaDropDownItem?.link}>
                              {megaDropDownItem?.title}
                            </Link>
                          </li>
                        ))}
                      </ul>
                    )}
                  </li>
                ))}
              </ul>
            )}
          </li>
        ))}
      </ul>
    </>
  );
};

export default UniversityHeaderMenu;

