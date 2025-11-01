<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Extract FAQs from PDF and generate additional FAQs
        $faqs = [
            // Publishing Guide - Should be #1
            [
                'question' => 'How do I publish a resource on the Knowledge Hub?',
                'answer' => 'Follow these 5 simple steps to publish your resource: Step 1 - Log in to your account and click "Publish a Resource" in the navigation menu or go to your account dashboard. Step 2 - Fill in the required fields including title, description (minimum 150 words), associated authors, and author affiliation. You can upload a document and let AI automatically extract the description, authors, and affiliation. Step 3 - Select thematic area, sub-theme, category, and at least one tag/health topic. Add optional metadata like DOI, ISSN, ISBN, publisher, license, and funder information. Step 4 - Upload your document file(s) or provide an external link URL. You can also upload a cover image or let the system extract it from your PDF. Step 5 - Review your submission and click "Submit". Your publication will be reviewed by administrators and you\'ll receive an email notification once it\'s approved or if any changes are needed.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // General FAQs about the Knowledge Hub
            [
                'question' => 'What is the Africa CDC Knowledge Hub / Knowledge Management Portal (KMP)?',
                'answer' => 'The Africa CDC Knowledge Hub is a comprehensive digital platform that provides access to public health resources, publications, research, tools, and information to support health professionals, researchers, and policymakers across Africa.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Who can use the Knowledge Hub?',
                'answer' => 'The Knowledge Hub is accessible to health professionals, researchers, students, policymakers, and anyone interested in public health information. Some content may require registration or have specific access restrictions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I register for an account?',
                'answer' => 'Click on the "Register" button in the top navigation menu. Fill in your details including name, email, country, and create a password. You can also register using your Microsoft, Google, or LinkedIn account for faster access.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I search for publications?',
                'answer' => 'Use the search bar at the top of the page to search by keywords, titles, or authors. You can also use advanced filters to search by thematic area, country, publication type, year, and tags to refine your results.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I submit my own publications?',
                'answer' => 'Yes! Registered users can submit publications through the "Publish" section. Your submission will be reviewed by administrators before being published. Make sure to include all required information and relevant tags.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I request specific content?',
                'answer' => 'Navigate to the "Support" menu and click on "Content Request". Fill out the form with details about the content you need, including subject, description, and your contact information. Our team will review and respond to your request.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What file formats are supported for uploads?',
                'answer' => 'We support various file formats including PDF, DOC, DOCX, and TXT files. PDF files can also have their cover images automatically extracted. Maximum file size is typically 10MB per file.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I download publications?',
                'answer' => 'Once you find a publication, click on it to view the details page. If the publication has downloadable attachments, you will see a "Download" button. Click it to download the file to your device.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I comment on publications?',
                'answer' => 'Yes! Registered users can comment on publications. Comments are moderated before being displayed. Click on a publication to view it, then scroll to the comments section to add your thoughts or questions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I save favorite publications?',
                'answer' => 'When viewing a publication, click the heart icon or "Add to Favorites" button. You can access all your saved favorites by going to your account dashboard and clicking on "Favorites".',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What is the difference between thematic areas and tags?',
                'answer' => 'Thematic areas are broad categories that organize content (e.g., Disease Surveillance, Health Systems). Tags are more specific keywords that help with detailed categorization and searching (e.g., COVID-19, Vaccination, Data Analytics).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I update my profile information?',
                'answer' => 'Log in to your account and click on your profile icon in the top right corner. Select "Profile" from the dropdown menu. You can update your name, email, country, ORCID, and other profile details.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I edit or delete my submitted publications?',
                'answer' => 'Yes, but only if your publication has not been approved yet. Once approved, you cannot edit or delete it. To edit, go to "My Publications" in your account dashboard and click the edit button for unpublished items.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I submit a new version of an existing publication?',
                'answer' => 'If version submission is enabled, go to the publication details page and click "Submit Version". Fill out the form with the updated information. The new version will be linked to the original publication.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What information is required when submitting a publication?',
                'answer' => 'Required fields typically include: title, description (minimum 150 words), associated authors, author affiliation, thematic area, sub-theme, category, and at least one tag/health topic. Some fields may vary based on publication type.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How does the AI description feature work?',
                'answer' => 'When you upload a document, our AI automatically extracts and generates a description/abstract from the content. It can also extract author names and affiliations. You can review and edit the AI-generated content before submitting.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What metadata can I add to publications?',
                'answer' => 'You can add metadata including DOI, ISSN, ISBN, Publisher, License/Copyright information, Funder, and for journal articles: Journal Name, Volume, Issue, and Pages. These fields help with proper citation and discoverability.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I contact support if I have issues?',
                'answer' => 'You can contact support through the "Content Request" form in the Support menu, or by emailing the support team. For technical issues, please include details about the problem you are experiencing.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Are there any usage restrictions or copyrights?',
                'answer' => 'Each publication has its own license and copyright information displayed on its details page. Please respect the license terms for each resource. Some publications may be available under Creative Commons licenses, while others may have specific usage restrictions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I filter publications by country or region?',
                'answer' => 'Use the advanced search filters on the search page. You can select specific countries or regions from the dropdown menus. You can also filter by thematic area, author, publication type, and year.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What is ORCID and why should I add it?',
                'answer' => 'ORCID (Open Researcher and Contributor ID) is a persistent identifier for researchers. Adding your ORCID links your profile to your publications and helps with proper attribution. Your ORCID will be displayed on your author profile page.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What should I do if my publication is rejected?',
                'answer' => 'If your publication is rejected, you will receive an email notification with details about the rejection reason. You can review the feedback, make necessary corrections, and resubmit your publication. Go to "My Publications" in your account dashboard to view rejected items and edit them. Ensure all required fields are properly filled and that your content meets the platform guidelines before resubmitting.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What upcoming features are planned for the Knowledge Hub?',
                'answer' => 'We are continuously improving the platform with new features. Upcoming enhancements include: AI-powered Full Library Chat - interact with our entire knowledge base using natural language queries powered by local Large Language Models (LLMs). Individual Document Chat - chat directly with specific documents to get instant answers and insights. These AI features will help you quickly find information, summarize content, and get answers to your questions without manually browsing through resources. All AI features will be powered by our own local LLMs to ensure data privacy and security.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How will the AI chat features work?',
                'answer' => 'The AI chat features will allow you to interact with the Knowledge Hub in natural language. You can ask questions about the entire library or specific documents, and the AI will provide relevant answers based on the content. The AI is powered by local Large Language Models, ensuring your queries and data remain private and secure. These features will help you quickly find information, understand complex documents, and get instant answers to your research questions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What are Communities of Practice and how do I join?',
                'answer' => 'Communities of Practice (CoPs) are groups of professionals who share a common interest in specific public health topics or areas of expertise. They provide a platform for networking, knowledge sharing, collaboration, and professional development. To join a Community of Practice, browse the available communities in the platform, find one that matches your interests or expertise, and click "Join" if you meet the membership criteria. Some communities may be open to all registered users, while others may have specific qualification requirements.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Who qualifies to join Communities of Practice?',
                'answer' => 'Qualification requirements vary by community. Generally, Communities of Practice are open to health professionals, researchers, practitioners, policymakers, and experts in relevant fields. Some communities may require specific qualifications such as professional experience, educational background, or area of expertise. Requirements are typically outlined in each community\'s description. All registered users can view available communities, and qualified members will be able to join and participate in discussions, share resources, and collaborate with peers.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What are forums and why is forum participation important?',
                'answer' => 'Forums are discussion spaces where members can ask questions, share insights, discuss topics, and engage in professional conversations about public health issues. Forum participation is important because it facilitates knowledge exchange, enables peer learning, helps solve problems collaboratively, builds professional networks, and contributes to the collective knowledge of the public health community. By participating in forums, you can gain insights from experienced professionals, share your expertise, stay updated on current issues, and contribute to advancing public health practice and research.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I participate in forums?',
                'answer' => 'To participate in forums, first ensure you are logged in to your account. Browse available forum discussions or create a new thread by clicking "Create Discussion". You can reply to existing threads by clicking on a discussion topic and adding your comment or response. Use the rich text editor to format your posts, include links, or attach relevant files. Be respectful, constructive, and follow community guidelines. Your contributions will be moderated before being displayed to ensure quality and appropriateness of content.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Is there a mobile app for the Knowledge Hub?',
                'answer' => 'Yes! The Africa CDC Knowledge Hub mobile app is available for Android devices. You can download it from the Google Play Store: https://play.google.com/store/apps/details?id=com.africacdc.khubmobile. The app allows you to publish health content, engage in expert forums, utilize AI-powered document comparison, and share documents and resources on the go. An iOS version is coming soon and will be available on the Apple App Store.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert FAQs into the database
        DB::table('faqs')->insert($faqs);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all FAQs (you may want to adjust this based on your needs)
        DB::table('faqs')->truncate();
    }
};
