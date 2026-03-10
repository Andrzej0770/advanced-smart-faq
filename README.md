# 🚀 Advanced Smart FAQ

**Advanced Smart FAQ** is a modern and lightweight WordPress plugin for creating beautiful FAQ sections with an accordion interface and SEO-friendly structured data.

Perfect for documentation pages, product pages, and support sections.

---

## ✨ Features

* 📚 Custom **FAQ Post Type**
* 🧩 Easy integration using shortcode
* 🎯 Clean **accordion UI**
* 🔎 Optional **real-time FAQ search**
* 🏷 Support for **FAQ categories**
* ⚡ Lightweight and fast
* 📱 Fully **responsive design**
* 🧠 Built-in **Schema.org FAQ structured data** for SEO
* ⚙️ Simple **admin settings page**

---

## 📦 Installation

1. Download the plugin or clone this repository.
2. Upload the plugin folder to:

```
/wp-content/plugins/
```

3. Activate the plugin in the **WordPress Admin → Plugins** page.

---

## 🧑‍💻 Usage

Add the FAQ block anywhere using the shortcode:

```
[smart_faq]
```

Example with options:

```
[smart_faq limit="10"]
```

You can also filter by category:

```
[smart_faq category="general"]
```

---

## 🖥 Example

The plugin displays questions in a modern accordion style:

```
❓ What is this plugin?
Click the question to reveal the answer.

❓ Is it SEO friendly?
Yes! It automatically adds FAQ structured data.
```

---

## ⚙️ Plugin Settings

After activation go to:

```
WordPress Admin → Settings → Smart FAQ
```

Available options include:

* Enable / Disable FAQ Schema
* FAQ display settings
* Search functionality

---

## 📂 Project Structure

```
advanced-smart-faq/
│
├── advanced-smart-faq.php
├── readme.txt
├── includes/
│   ├── class-faq-post-type.php
│   ├── class-faq-shortcode.php
│   ├── class-faq-schema.php
│   └── class-faq-admin.php
│
├── assets/
│   ├── css/
│   └── js/
```

---

## 🧠 SEO Benefits

The plugin automatically generates **FAQPage structured data** following the Schema.org standard.

This helps search engines like Google display rich FAQ results directly in search.

---

## 🔧 Requirements

* WordPress 6.0+
* PHP 7.4+

---

## 🤝 Contributing

Contributions are welcome!

If you want to improve the plugin:

1. Fork the repository
2. Create a new branch
3. Submit a pull request

---

## 📜 License

This plugin is licensed under the **GPL v2 or later**.

---

## 👨‍💻 Author

Developed by **Andrij Petrenko**

GitHub:
https://github.com/Andrzej0770

---

⭐ If you like this plugin, consider giving the repository a star!
