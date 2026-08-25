import ElementsBreadcrumb from '@/components/common/Breadcrumb/ElementsBreadcrumb';
import React from 'react';
import NewsLetterStyleOne from './NewsLetterStyleOne';
import NewsLetterStyleTwo from './NewsLetterStyleTwo';
import NewsLetterStyleThree from './NewsLetterStyleThree';

const ElementNewsLetterMain = () => {
    return (
        <>
          <ElementsBreadcrumb title='Newsletter' subTitle='Newsletter style'/>
          <NewsLetterStyleOne/>
          <NewsLetterStyleTwo/>
          <NewsLetterStyleThree/>
        </>
    );
}
export default ElementNewsLetterMain;