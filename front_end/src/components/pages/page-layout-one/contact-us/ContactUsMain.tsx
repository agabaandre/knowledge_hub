import React from 'react';
import Breadcrumbs from '../../../common/Breadcrumb/Breadcrumbs';
import ContactFormArea from './ContactFormArea';
import ContactAddressArea from './ContactAddressArea';

const ContactUsMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Contact Us' />
            <ContactFormArea />
            <ContactAddressArea />
        </>
    );
};

export default ContactUsMain;