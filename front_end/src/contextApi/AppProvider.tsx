"use client";

import React, { createContext, useState } from "react";
import { AppContextType } from "@/interFace/interFace";

export const AppContext = createContext<AppContextType | undefined>(undefined);
const AppProvider = ({ children }: { children: React.ReactNode }) => {
  const [sideMenuOpen, setSideMenuOpen] = useState<boolean>(false);
  const [scrollDirection, setScrollDirection] = useState("up");
  const [inputValue, setInputValue] = useState<string>("");
  const [filterType, setFilterType] = useState<string>("");
  const [isVideoOpen, setIsVideoOpen] = useState(false);
  const openVideoModal = () => setIsVideoOpen(!isVideoOpen);
  const [isOpen, setIsOpen] = useState(false);
  const [openSidebar, setOpenSidebar] = useState(false);

  // Toggle Functions
  const toggleSideMenu = () => setSideMenuOpen(!sideMenuOpen);
  const toggleSidebarMenu = () => setOpenSidebar(!openSidebar);
  const toggleOpen = () => setIsOpen((prev) => !prev);

  const contextValue: AppContextType = {
    scrollDirection,
    setScrollDirection,
    sideMenuOpen,
    toggleSideMenu,
    setSideMenuOpen,
    inputValue,
    setInputValue,
    filterType,
    setFilterType,
    isVideoOpen,
    setIsVideoOpen,
    openVideoModal,
    isOpen,
    toggleOpen,
    openSidebar,
    setOpenSidebar,
    toggleSidebarMenu
  };

  return (
    <AppContext.Provider value={contextValue}>{children}</AppContext.Provider>
  );
};

export default AppProvider;
