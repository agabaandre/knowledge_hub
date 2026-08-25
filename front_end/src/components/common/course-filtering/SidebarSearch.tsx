import React from 'react';

const SidebarSearch = () => {
    return (
        <>
            <div className="bd-course-widget widget-search">
                <h5 className="bd-widget-title mb-20">Search</h5>
                <div className="bd-widget-content">
                    <div className="bd-sidebar-search">
                        <form className="bd-sidebar-search-form" action="#" method="get">
                            <input type="text" defaultValue="" name="s" placeholder="Search" />
                            <button type="submit"> <i className="far fa-search"></i> </button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
};

export default SidebarSearch;