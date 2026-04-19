<?php
/**
 * بيانات دخول لوحة التحكم فقط — غيّر اسم المستخدم وكلمة المرور بعد الرفع.
 *
 * لتوليد hash جديد من الطرفية:
 *   php -r "echo password_hash('كلمة_السر_الجديدة', PASSWORD_DEFAULT);"
 * ثم ضع الناتج في password_hash أدناه.
 */
return [
    'username' => 'admin',
    /** كلمة المرور الافتراضية: FarmDash2026! — غيّرها فوراً */
    'password_hash' => '$2y$12$Hnzu1N4j0Vzr14t9vKC9AOT0kFRzh3eDDEKSsZXqgj1oncDnmiuFG',
];
