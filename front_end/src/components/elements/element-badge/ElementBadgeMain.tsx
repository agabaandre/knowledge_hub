import ElementsBreadcrumb from '@/components/common/Breadcrumb/ElementsBreadcrumb';
import React from 'react';
import ButtonStyleOne from './ButtonStyleOne';
import ButtonStyleTwo from './ButtonStyleTwo';

const ElementBadgeMain = () => {
    return (
        <>
            <ElementsBreadcrumb title='Badge' subTitle='Badge style'/>
            <ButtonStyleOne/>
            <ButtonStyleTwo/>
        </>
    );
};

export default ElementBadgeMain;