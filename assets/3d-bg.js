document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('canvas-3d');
    if (!container) return;

    // SCENE SETUP
    const scene = new THREE.Scene();
    // No background color, let transparency show through to CSS background

    // CAMERA
    const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.z = 5;

    // RENDERER
    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    container.appendChild(renderer.domElement);

    // OBJECTS (Thunder Theme: Geometric shapes)
    const isMobile = window.innerWidth < 768;
    const geometry = new THREE.IcosahedronGeometry(isMobile ? 0.6 : 1, 0);
    const particleCount = isMobile ? 6 : 15; // Reduce count significantly on mobile

    // Material 1: Red Wireframe
    const materialWire = new THREE.MeshBasicMaterial({
        color: 0xdc2626, // Red-600
        wireframe: true,
        transparent: true,
        opacity: 0.3
    });

    // Material 2: Orange Solid
    const materialSolid = new THREE.MeshPhongMaterial({
        color: 0xff9900, // Orange
        shininess: 100,
        transparent: true,
        opacity: 0.8
    });

    // Create multiple shapes
    const shapes = [];

    for (let i = 0; i < particleCount; i++) {
        const isWire = Math.random() > 0.5;
        const mesh = new THREE.Mesh(
            geometry,
            isWire ? materialWire : materialSolid
        );

        // Random positions scattered
        mesh.position.x = (Math.random() - 0.5) * 15;
        mesh.position.y = (Math.random() - 0.5) * 10;
        mesh.position.z = (Math.random() - 0.5) * 10 - 2;

        // Random rotation
        mesh.rotation.x = Math.random() * Math.PI;
        mesh.rotation.y = Math.random() * Math.PI;

        // Random scale
        const scale = Math.random() * 0.5 + 0.2;
        mesh.scale.set(scale, scale, scale);

        scene.add(mesh);
        shapes.push({
            mesh,
            rotSpeedX: (Math.random() - 0.5) * 0.02,
            rotSpeedY: (Math.random() - 0.5) * 0.02,
            floatSpeed: Math.random() * 0.01 + 0.005,
            floatOffset: Math.random() * Math.PI * 2
        });
    }

    // LIGHTING
    const ambientLight = new THREE.AmbientLight(0x404040, 2); // Soft white light
    scene.add(ambientLight);

    const pointLight = new THREE.PointLight(0xffffff, 1, 100);
    pointLight.position.set(10, 10, 10);
    scene.add(pointLight);

    const redLight = new THREE.PointLight(0xdc2626, 2, 50);
    redLight.position.set(-5, -5, 5);
    scene.add(redLight);

    // ANIMATION LOOP
    let time = 0;
    function animate() {
        requestAnimationFrame(animate);
        time += 0.01;

        shapes.forEach(item => {
            // Rotate
            item.mesh.rotation.x += item.rotSpeedX;
            item.mesh.rotation.y += item.rotSpeedY;

            // Float up and down
            item.mesh.position.y += Math.sin(time + item.floatOffset) * 0.005;
        });

        // Mouse parallax effect (dampened)
        camera.position.x += (mouseX * 0.5 - camera.position.x) * 0.05;
        camera.position.y += (-mouseY * 0.5 - camera.position.y) * 0.05;
        camera.lookAt(scene.position);

        renderer.render(scene, camera);
    }

    // MOUSE INTERACTION
    let mouseX = 0;
    let mouseY = 0;

    document.addEventListener('mousemove', (event) => {
        mouseX = (event.clientX / window.innerWidth) * 2 - 1;
        mouseY = (event.clientY / window.innerHeight) * 2 - 1;
    });

    // RESIZE HANDLER
    window.addEventListener('resize', () => {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });

    animate();
});
