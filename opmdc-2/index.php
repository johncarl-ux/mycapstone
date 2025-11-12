<?php
// Homepage: load announcements from DB
$mysqli = include __DIR__ . '/db.php';
$announcements = [];
$res = $mysqli->query("SELECT id, title, body, created_by, created_at FROM announcements ORDER BY created_at DESC LIMIT 10");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $announcements[] = $row;
    }
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OPMDC Homepage - Enhanced</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Use a more modern font */
    body { font-family: 'Poppins', sans-serif; }
    .hero-section {
      background-image: linear-gradient(rgba(17, 24, 39, 0.7), rgba(17, 24, 39, 0.7)),
        url('assets/image 2.jpg');
      background-size: cover;
      background-position: center;
    }
    .cta-section {
        background-image: linear-gradient(to right, rgba(59, 130, 246, 0.9), rgba(37, 99, 235, 0.9)), url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=2670&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
    }
    .reveal-on-scroll { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
    .reveal-on-scroll.is-visible { opacity: 1; transform: translateY(0); }
    .header-scrolled { background-color: rgba(255, 255, 255, 0.95); box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); padding-top: 0.5rem; padding-bottom: 0.5rem; }
  </style>
</head>
<body class="bg-white text-gray-800 antialiased">
  
  <header id="main-header" class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 transition-all duration-300">
    <div class="container mx-auto px-6 py-4">
      <div class="flex justify-between items-center">
        <a href="#" class="text-2xl font-bold text-gray-900">OPMDC</a>
        <nav class="hidden md:flex items-center space-x-8">
          <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors duration-300">Home</a>
          <a href="#mission" class="text-gray-600 hover:text-blue-600 transition-colors duration-300">About</a>
          <a href="#services" class="text-gray-600 hover:text-blue-600 transition-colors duration-300">Services</a>
          <a href="login.html" class="bg-blue-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-blue-700 transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">Login</a>
        </nav>
        <button id="mobile-menu-button" class="md:hidden text-gray-800 focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
      </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden px-6 pt-2 pb-4">
      <a href="#" class="block text-gray-600 hover:text-blue-600 py-2">Home</a>
      <a href="#mission" class="block text-gray-600 hover:text-blue-600 py-2">About</a>
      <a href="#services" class="block text-gray-600 hover:text-blue-600 py-2">Services</a>
      <a href="login.html" class="block bg-blue-600 text-white font-semibold mt-2 px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center">Login</a>
    </div>
  </header>

  <main>
    <section class="hero-section text-white min-h-screen flex items-center justify-center -mt-20 pt-20">
      <div class="text-center max-w-4xl mx-auto px-6">
        <h1 class="text-4xl md:text-6xl font-extrabold mb-4 animate-fade-in-down" style="animation-delay: 0.2s;">Seamless Governance, Unified Communities</h1>
        <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-2xl mx-auto animate-fade-in-down" style="animation-delay: 0.4s;">OPMDC connects Barangays and the Municipal Office in one powerful, unified system. Drive efficiency, transparency, and real-time communication.</p>
        <a href="login.html" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-lg text-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg animate-fade-in-up" style="animation-delay: 0.6s;">Access Your Portal</a>
      </div>
    </section>

    <!-- Announcements section: populated from DB -->
    <section id="announcements" class="py-12 bg-slate-50">
      <div class="container mx-auto px-6">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-gray-900">Announcements</h2>
          <p class="text-gray-600">Latest messages from the Municipal Office and OPMDC staff.</p>
        </div>

        <div class="max-w-4xl mx-auto space-y-4">
          <?php if (count($announcements) === 0): ?>
            <p class="text-center text-gray-500">No announcements at this time.</p>
          <?php else: ?>
            <?php foreach ($announcements as $a): ?>
              <article class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($a['title']); ?></h3>
                    <p class="text-sm text-gray-600 mt-1">by <?php echo htmlspecialchars($a['created_by'] ?? 'Staff'); ?></p>
                  </div>
                  <div class="text-xs text-gray-500 text-right">
                    <?php echo date('M j, Y \a\t g:i A', strtotime($a['created_at'])); ?>
                  </div>
                </div>
                <div class="mt-4 text-gray-700 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($a['body'])); ?></div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <section id="mission" class="py-16 md:py-24 bg-white">
      <div class="container mx-auto px-6 text-center reveal-on-scroll">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Mission: Smarter Local Governance</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">We are dedicated to empowering local government units with the technology to automate processes, centralize data, and foster better communication, creating more responsive and effective public service for everyone.</p>
      </div>
    </section>

    <section id="services" class="py-16 md:py-24 bg-slate-50">
      <div class="container mx-auto px-6">
        <div class="text-center mb-12 reveal-on-scroll">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Efficient. Transparent. Connected.</h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto mt-4">Our platform streamlines communication and data management for LGUs.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal-on-scroll" style="transition-delay: 100ms;">
            <div class="bg-blue-100 text-blue-600 rounded-full h-12 w-12 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4M4 7s0 4 8 4 8-4 8-4"/></svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Centralized Data</h3>
            <p class="text-gray-600">Access all municipal and barangay data from a single, secure dashboard.</p>
          </div>
          <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal-on-scroll" style="transition-delay: 200ms;">
            <div class="bg-blue-100 text-blue-600 rounded-full h-12 w-12 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Real-time Communication</h3>
            <p class="text-gray-600">Facilitate instant messaging and announcements for faster response times.</p>
          </div>
          <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal-on-scroll" style="transition-delay: 300ms;">
            <div class="bg-blue-100 text-blue-600 rounded-full h-12 w-12 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Automated Reports</h3>
            <p class="text-gray-600">Generate comprehensive population, financial, and activity reports with a few clicks.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="testimonials" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 reveal-on-scroll">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Trusted by Local Leaders</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mt-4">Hear what officials have to say about OPMDC.</p>
            </div>
            <div class="max-w-3xl mx-auto text-center relative">
                <div id="testimonial-container">
                    <div class="testimonial-item bg-slate-50 p-8 rounded-lg shadow-sm">
                        <blockquote class="text-xl italic text-gray-700">"Implementing OPMDC was a game-changer... The transparency and efficiency gains are incredible."</blockquote>
                        <div class="mt-6 flex items-center justify-center">
                            <img class="h-12 w-12 rounded-full object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Testimonial">
                            <div class="ml-4 text-left">
                                <p class="font-semibold text-gray-900">Noel Bitrics Luistro</p>
                                <p class="text-gray-600">Municipal Mayor, Mabini</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-slate-50 p-8 rounded-lg shadow-sm hidden">
                        <blockquote class="text-xl italic text-gray-700">"Our community engagement has never been better. OPMDC bridges the gap between officials and citizens perfectly."</blockquote>
                        <div class="mt-6 flex items-center justify-center">
                            <img class="h-12 w-12 rounded-full object-cover" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Testimonial">
                            <div class="ml-4 text-left">
                                <p class="font-semibold text-gray-900">Maria Clara</p>
                                <p class="text-gray-600">Barangay Captain, Sampaloc</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button id="prev-testimonial" class="absolute top-1/2 -left-4 md:-left-12 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">&lt;</button>
                <button id="next-testimonial" class="absolute top-1/2 -right-4 md:-right-12 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">&gt;</button>
            </div>
        </div>
    </section>

    <section class="cta-section text-white py-16 md:py-24">
        <div class="container mx-auto px-6 text-center reveal-on-scroll">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Ready to Transform Your Governance?</h2>
            <p class="text-lg md:text-xl text-blue-100 mb-8 max-w-2xl mx-auto">Join the growing number of LGUs leveraging OPMDC to build a more connected and efficient community.</p>
            <a href="login.html" class="bg-white text-blue-600 font-bold py-3 px-8 rounded-lg text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">Request a Demo</a>
        </div>
    </section>

  </main>

  <footer class="bg-gray-900 text-white">
    <div class="container mx-auto px-6 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
              <h3 class="text-xl font-bold mb-4">OPMDC</h3>
              <p class="text-gray-400">Connecting local government for a brighter, more efficient future.</p>
          </div>
          <div>
              <h3 class="font-semibold mb-4">Quick Links</h3>
              <ul class="space-y-2 text-gray-400">
                  <li><a href="#mission" class="hover:text-white transition-colors">About Us</a></li>
                  <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                  <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
              </ul>
          </div>
      <div>
        <h3 class="font-semibold mb-4">Contact</h3>
        <ul class="space-y-2 text-gray-400">
          <li><p>Land Luna No.: (043) 410 1643</p></li>
          <li><p>Mobile kps.: 091946305; 09191778305; 09152016244; 09688799250; 09996893577</p></li>
          <li><p><a href="mailto:mabimargas.philippines@gmail.com" class="text-gray-400 hover:text-white">mabimargas.philippines@gmail.com</a></p></li>
          <li><p>Office: Office of the MPDC</p></li>
          <li><p>New Municipal Building, Municipal Hall, Poblacion Mabini, Batangas 4202</p></li>
        </ul>
      </div>
          <div>
              <h3 class="font-semibold mb-4">Follow Us</h3>
              <div class="flex space-x-4">
                  <a href="#" class="text-gray-400 hover:text-white transition-colors">
                      <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                  </a>
                  </div>
          </div>
      </div>
      <div class="mt-12 border-t border-gray-800 pt-6 text-center">
        <p class="text-gray-500">&copy; 2025 OPMDC. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const mobileMenuButton = document.getElementById('mobile-menu-button');
      const mobileMenu = document.getElementById('mobile-menu');
      if (mobileMenuButton && mobileMenu) mobileMenuButton.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

      // reveal-on-scroll
      const elementsToReveal = document.querySelectorAll('.reveal-on-scroll');
      if (elementsToReveal.length) {
        const observer = new IntersectionObserver((entries) => { entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('is-visible'); }); }, { threshold: 0.1 });
        elementsToReveal.forEach(e => observer.observe(e));
      }
    });
  </script>
  
</body>
</html>
