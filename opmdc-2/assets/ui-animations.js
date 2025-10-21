// Shared staged reveal script
(function(){
  function stagedReveal(){
    try{
      var mq = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
      var nodes = Array.prototype.slice.call(document.querySelectorAll('[data-reveal]'));
      if (!nodes.length) return;
      if (mq && mq.matches) { nodes.forEach(function(n){ n.classList.add('visible'); }); return; }
      var order = [].concat(
        nodes.filter(function(n){ return n.getAttribute('data-reveal')==='sidebar'; }),
        nodes.filter(function(n){ return n.getAttribute('data-reveal')==='header'; }),
        nodes.filter(function(n){ return n.getAttribute('data-reveal')==='group'; })
      );
      order.forEach(function(n, i){
        requestAnimationFrame(function(){ setTimeout(function(){ n.classList.add('visible'); }, 100 + i*120); });
      });
    }catch(e){/* silent */}
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', stagedReveal); else stagedReveal();
})();
