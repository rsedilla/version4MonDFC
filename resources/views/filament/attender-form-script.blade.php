<script>
function toggleSection(sectionClass) {
    const elements = document.querySelectorAll('.' + sectionClass);
    const isVisible = elements[0] ? elements[0].style.display !== 'none' : false;
    
    elements.forEach(element => {
        element.style.display = isVisible ? 'none' : 'block';
    });
}

// Auto-show member section by default
document.addEventListener('DOMContentLoaded', function() {
    const memberSection = document.getElementById('member-section');
    if (memberSection) {
        memberSection.style.display = 'block';
    }
});
</script>

<style>
.collapsible-header {
    cursor: pointer;
    user-select: none;
    transition: all 0.3s ease;
}

.collapsible-header:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}

.form-section {
    transition: all 0.3s ease;
}
</style>
