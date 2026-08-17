<?php
declare(strict_types=1);

$controls = [
    ['id' => 'battery', 'label' => 'BATTERY', 'description' => 'Connects the main aircraft battery to the hot battery bus.', 'group' => 'Electrical'],
    ['id' => 'apu', 'label' => 'APU START', 'description' => 'Starts the auxiliary power unit for electrical power and pneumatic air.', 'group' => 'Electrical'],
    ['id' => 'beacon', 'label' => 'BEACON', 'description' => 'Turns the red anti-collision beacon on or off.', 'group' => 'Lighting'],
    ['id' => 'landingLights', 'label' => 'LANDING LIGHTS', 'description' => 'Extends and illuminates the landing lights.', 'group' => 'Lighting'],
    ['id' => 'gear', 'label' => 'GEAR', 'description' => 'Raises or lowers the landing gear. The gear is locked down when three green lights show.', 'group' => 'Flight'],
    ['id' => 'flaps', 'label' => 'FLAPS', 'description' => 'Cycles the trailing-edge flap setting for takeoff and landing.', 'group' => 'Flight'],
    ['id' => 'speedbrake', 'label' => 'SPEEDBRAKE', 'description' => 'Raises wing spoilers to reduce lift and increase drag.', 'group' => 'Flight'],
    ['id' => 'door', 'label' => 'L1 DOOR', 'description' => 'Opens or closes the forward-left passenger entry door.', 'group' => 'Doors'],
    ['id' => 'autopilot', 'label' => 'CMD A', 'description' => 'Engages or disengages the left autopilot command channel.', 'group' => 'Autoflight'],
    ['id' => 'heading', 'label' => 'HDG HOLD', 'description' => 'Commands the autopilot to maintain the selected heading.', 'group' => 'Autoflight'],
    ['id' => 'engines', 'label' => 'ENGINE START', 'description' => 'Starts or shuts down all four engines for this demonstration.', 'group' => 'Engines'],
];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>747-8 Interactive Flight Deck</title>
  <style>
    :root{--ink:#e8f2f4;--muted:#8da2a8;--panel:#101719;--panel2:#172124;--line:#334247;--green:#83efab;--amber:#ffbf5a;--blue:#64b5f6}
    *{box-sizing:border-box}html,body{height:100%;margin:0;background:#071012;color:var(--ink);font-family:Inter,ui-sans-serif,system-ui,sans-serif;overflow:hidden}
    canvas{display:block}.topbar{position:fixed;z-index:5;left:0;right:0;top:0;height:64px;display:flex;align-items:center;gap:18px;padding:0 22px;background:linear-gradient(180deg,rgba(5,12,14,.98),rgba(5,12,14,.82));border-bottom:1px solid #243136;backdrop-filter:blur(10px)}
    .brand{font-size:12px;letter-spacing:.2em}.brand b{font-size:18px;letter-spacing:.05em;margin-right:10px}.mode{color:var(--green);font:11px ui-monospace,monospace}.spacer{flex:1}.topbtn{border:1px solid var(--line);background:#152024;color:var(--ink);border-radius:4px;padding:9px 12px;cursor:pointer}.topbtn:hover,.topbtn.active{border-color:var(--green);color:var(--green)}
    #viewport{position:absolute;inset:64px 322px 0 0}.side{position:fixed;z-index:4;right:0;top:64px;bottom:0;width:322px;background:rgba(12,19,21,.97);border-left:1px solid #263438;padding:18px;overflow:auto}.side h1{font-size:18px;margin:0 0 5px}.sub{font-size:12px;color:var(--muted);line-height:1.5;margin-bottom:18px}
    .control{width:100%;text-align:left;padding:12px;margin:0 0 8px;border:1px solid #2c3b40;border-radius:6px;background:#121c1f;color:var(--ink);cursor:pointer;transition:.18s}.control:hover{transform:translateX(-3px);border-color:#73898e}.control.on{border-color:var(--green);box-shadow:inset 3px 0 var(--green)}.control strong{display:flex;justify-content:space-between;font-size:12px;letter-spacing:.07em}.control small{display:block;color:var(--muted);margin-top:5px;line-height:1.35}.control .state{color:var(--amber)}.control.on .state{color:var(--green)}
    .group{font:10px ui-monospace,monospace;color:#6f858b;letter-spacing:.16em;margin:18px 0 8px}.hud{position:absolute;z-index:3;left:18px;bottom:18px;padding:11px 14px;background:rgba(7,13,15,.78);border:1px solid #334247;border-radius:5px;font:11px ui-monospace,monospace;color:#b9c9cd;pointer-events:none}.hint{position:absolute;z-index:3;top:18px;left:50%;transform:translateX(-50%);background:rgba(7,13,15,.8);border:1px solid #334247;padding:9px 13px;border-radius:4px;font-size:11px;color:#bdcdd0;pointer-events:none;transition:.3s}.hint.hide{opacity:0}.tooltip{position:fixed;display:none;z-index:10;max-width:260px;padding:9px 11px;background:#e9f2ef;color:#111;border-radius:4px;font-size:12px;box-shadow:0 8px 30px #0008;pointer-events:none}
    .legend{display:flex;gap:10px;font-size:10px;color:var(--muted);border-top:1px solid #263438;margin-top:18px;padding-top:14px}.dot{width:8px;height:8px;border-radius:50%;background:var(--green);display:inline-block;margin-right:4px}
    @media(max-width:850px){#viewport{right:0;bottom:42vh}.side{top:58vh;left:0;width:auto;border-left:0;border-top:1px solid #263438}.topbar{height:56px}.brand span,.mode{display:none}}
  </style>
</head>
<body>
<header class="topbar"><div class="brand"><b>747–8</b><span>INTERACTIVE FLIGHT DECK</span></div><div class="mode">TRAINING DEMONSTRATOR · NOT FOR FLIGHT</div><div class="spacer"></div><button class="topbtn active" data-view="cockpit">COCKPIT</button><button class="topbtn" data-view="external">EXTERNAL</button><button class="topbtn" id="reset">RESET</button></header>
<main id="viewport"><div class="hint" id="hint">Drag to look · Scroll to zoom · Click illuminated controls</div><div class="hud" id="hud">BAT 24V &nbsp;|&nbsp; APU OFF &nbsp;|&nbsp; GEAR DOWN<br>IAS 000 &nbsp; HDG 280 &nbsp; ALT 00000</div></main>
<aside class="side"><h1>Systems & controls</h1><div class="sub">Select a control to operate it. Hover any illuminated cockpit object to identify it. This is an educational visualization, not a systems-accurate simulator.</div>
<?php $last=''; foreach ($controls as $c): if ($last !== $c['group']): $last=$c['group']; ?><div class="group"><?=htmlspecialchars($last)?></div><?php endif; ?>
<button class="control" data-control="<?=htmlspecialchars($c['id'])?>"><strong><?=htmlspecialchars($c['label'])?><span class="state">OFF</span></strong><small><?=htmlspecialchars($c['description'])?></small></button><?php endforeach; ?>
<div class="legend"><span><i class="dot"></i>active</span><span>Click objects or this panel</span></div></aside>
<div class="tooltip" id="tooltip"></div>
<script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.164.1/build/three.module.js","three/addons/":"https://cdn.jsdelivr.net/npm/three@0.164.1/examples/jsm/"}}</script>
<script type="module">
import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

const mount=document.querySelector('#viewport'), scene=new THREE.Scene();
scene.background=new THREE.Color(0x92b4c1); scene.fog=new THREE.Fog(0x92b4c1,70,220);
const camera=new THREE.PerspectiveCamera(55,mount.clientWidth/mount.clientHeight,.05,500);
const renderer=new THREE.WebGLRenderer({antialias:true}); renderer.setPixelRatio(Math.min(devicePixelRatio,2)); renderer.setSize(mount.clientWidth,mount.clientHeight); renderer.shadowMap.enabled=true; renderer.outputColorSpace=THREE.SRGBColorSpace; mount.prepend(renderer.domElement);
scene.add(new THREE.HemisphereLight(0xd8f1ff,0x243130,2.1)); const sun=new THREE.DirectionalLight(0xffffff,2.6); sun.position.set(-20,35,30); sun.castShadow=true; scene.add(sun);
const controls=new OrbitControls(camera,renderer.domElement); controls.enableDamping=true; controls.target.set(0,2,-2); controls.minDistance=.8; controls.maxDistance=95;
const M={dark:new THREE.MeshStandardMaterial({color:0x151c1d,roughness:.8}),panel:new THREE.MeshStandardMaterial({color:0x263033,roughness:.72}),metal:new THREE.MeshStandardMaterial({color:0xd6dde0,metalness:.65,roughness:.3}),white:new THREE.MeshStandardMaterial({color:0xf5f7f5,metalness:.25,roughness:.35}),blue:new THREE.MeshStandardMaterial({color:0x143c67,metalness:.25,roughness:.4}),glass:new THREE.MeshPhysicalMaterial({color:0x75b0c7,transmission:.3,transparent:true,opacity:.68,roughness:.15}),rubber:new THREE.MeshStandardMaterial({color:0x090b0b,roughness:.9}),green:new THREE.MeshStandardMaterial({color:0x3ce878,emissive:0x0b722d,emissiveIntensity:1}),amber:new THREE.MeshStandardMaterial({color:0xffa629,emissive:0x8a3e00,emissiveIntensity:1})};
const box=(n,s,p,mat=M.panel)=>{const o=new THREE.Mesh(new THREE.BoxGeometry(...s),mat);o.name=n;o.position.set(...p);o.castShadow=o.receiveShadow=true;return o};
const cyl=(n,r,l,p,rot=[0,0,0],mat=M.metal)=>{const o=new THREE.Mesh(new THREE.CylinderGeometry(r,r,l,20),mat);o.name=n;o.position.set(...p);o.rotation.set(...rot);o.castShadow=true;return o};
const plane=box('Apron',[400,.2,400],[0,-3,0],new THREE.MeshStandardMaterial({color:0x263436,roughness:1}));scene.add(plane);
for(let i=-4;i<=4;i++){const line=box('',[.15,.02,150],[i*10,-2.88,0],new THREE.MeshStandardMaterial({color:0x586669})); scene.add(line)}

const aircraft=new THREE.Group(), exteriorAnimated=new THREE.Group(), importedAircraft=new THREE.Group(); scene.add(aircraft,exteriorAnimated,importedAircraft);
const fuselage=new THREE.Mesh(new THREE.CapsuleGeometry(3.9,48,12,28),M.white); fuselage.rotation.x=Math.PI/2; fuselage.position.z=2; aircraft.add(fuselage);
const upper=new THREE.Mesh(new THREE.CapsuleGeometry(2.6,12,8,24),M.white); upper.rotation.x=Math.PI/2;upper.position.set(0,3.3,-16);aircraft.add(upper);
const wingGeo=new THREE.BufferGeometry();wingGeo.setAttribute('position',new THREE.Float32BufferAttribute([0,0,-4,-34,0,7,-30,0,12,0,0,5,34,0,7,30,0,12],3));wingGeo.setIndex([0,1,2,0,2,3,0,3,4,4,3,5]);wingGeo.computeVertexNormals();const wings=new THREE.Mesh(wingGeo,M.metal);wings.position.y=-.5;aircraft.add(wings);
const tail=box('Vertical stabilizer',[.7,9,10],[0,5,24],M.blue);tail.rotation.x=-.3;aircraft.add(tail);aircraft.add(box('',[20,.45,4],[0,2.4,23],M.metal));
for(const x of [-14,-7,7,14]){const e=cyl('Engine',2.05,5.3,[x,-2,x<0?4:4],[Math.PI/2,0,0],M.metal); aircraft.add(e);const fan=cyl('',1.55,.3,[x,-2,1.3],[Math.PI/2,0,0],M.dark);aircraft.add(fan)}
const gear=[]; function addGear(x,z){const g=new THREE.Group();g.position.set(x,-1.7,z);const strut=cyl('',.22,3,[0,-1,0],[],M.metal);g.add(strut);for(const dx of [-.55,.55]){const w=cyl('',.6,.38,[dx,-2.2,0],[0,0,Math.PI/2],M.rubber);g.add(w)}exteriorAnimated.add(g);gear.push(g)} addGear(0,-15);addGear(-4.5,7);addGear(4.5,7);
const door=box('L1 passenger door',[.15,2.8,1.65],[-3.88,1.1,-13.5],M.metal);exteriorAnimated.add(door);door.userData={control:'door',label:'L1 passenger door — opens outward and forward'};

const cockpit=new THREE.Group();scene.add(cockpit);cockpit.visible=true;
cockpit.add(box('Glare shield',[8,.35,1.2],[0,1,-2.5],M.dark));cockpit.add(box('Main instrument panel',[8,3,.45],[0,-.55,-2.75],M.panel));cockpit.add(box('Center pedestal',[2.2,1,4],[0,-1.9,-.3],M.panel));cockpit.add(box('Overhead panel',[6,.3,4],[0,3.45,-.5],M.panel));
for(const x of [-2.7,0,2.7]){const screen=box('Flight display',[2.2,1.55,.12],[x,-.25,-2.48],new THREE.MeshStandardMaterial({color:0x071818,emissive:0x063d45,emissiveIntensity:.8}));cockpit.add(screen)}
for(const x of [-3.2,3.2]){const window=box('Flight deck window',[2.8,1.9,.08],[x,2.1,-2.9],M.glass);window.rotation.z=x<0?.08:-.08;cockpit.add(window)}
const clickables=[]; const specs=[['battery',-2.3,3.28,-1.5,'Battery master'],['apu',-1.3,3.28,-1.5,'APU start selector'],['beacon',0,3.28,-1.5,'Anti-collision beacon'],['landingLights',1.3,3.28,-1.5,'Landing light switches'],['autopilot',-1.8,1.05,-2.15,'Autopilot command A'],['heading',-.6,1.05,-2.15,'Heading hold'],['gear',2.8,-1.0,-2.15,'Landing gear lever'],['flaps',1.0,-1.35,.4,'Flap lever'],['speedbrake',-1.0,-1.35,.4,'Speedbrake lever'],['engines',0,-1.36,-.55,'Four engine start switches']];
for(const [id,x,y,z,label] of specs){const b=box(label,[.55,.22,.35],[x,y,z],M.amber);b.userData={control:id,label};cockpit.add(b);clickables.push(b)}
const yoke=cyl('Control column',.12,1.3,[-2.4,-1.55,-1.2],[0,0,.15],M.metal);cockpit.add(yoke);const wheel=new THREE.Mesh(new THREE.TorusGeometry(.55,.11,10,24),M.dark);wheel.position.set(-2.55,-.95,-1.2);cockpit.add(wheel);

const state=Object.fromEntries([...document.querySelectorAll('[data-control]')].map(b=>[b.dataset.control,false]));state.gear=true;
function setControl(id,value=!state[id]){state[id]=value;document.querySelectorAll(`[data-control="${id}"]`).forEach(b=>{b.classList.toggle('on',value);b.querySelector('.state').textContent=value?'ON':'OFF'});clickables.filter(o=>o.userData.control===id).forEach(o=>o.material=value?M.green:M.amber);if(id==='gear') gear.forEach(g=>g.userData.target=value?0:Math.PI/2);if(id==='door')door.userData.target=value?1:0;if(id==='beacon')beacon.visible=value;if(id==='landingLights')landingLight.intensity=value?45:0;updateHud()}
function updateHud(){document.querySelector('#hud').innerHTML=`BAT ${state.battery?'28':'24'}V &nbsp;|&nbsp; APU ${state.apu?'RUN':'OFF'} &nbsp;|&nbsp; GEAR ${state.gear?'DOWN':'UP'}<br>IAS 000 &nbsp; HDG 280 &nbsp; ALT 00000 &nbsp; FLAPS ${state.flaps?'10':'0'}`}
document.querySelectorAll('[data-control]').forEach(b=>b.onclick=()=>setControl(b.dataset.control));setControl('gear',true);
const beacon=new THREE.PointLight(0xff1800,0,18);beacon.position.set(0,5,-2);exteriorAnimated.add(beacon);const landingLight=new THREE.SpotLight(0xffffff,0,80,.35,.4,1);landingLight.position.set(0,-1,-15);landingLight.target.position.set(0,-3,-60);exteriorAnimated.add(landingLight,landingLight.target);
const ray=new THREE.Raycaster(),mouse=new THREE.Vector2(),tooltip=document.querySelector('#tooltip');renderer.domElement.addEventListener('pointermove',e=>{const r=renderer.domElement.getBoundingClientRect();mouse.set((e.clientX-r.left)/r.width*2-1,-(e.clientY-r.top)/r.height*2+1);ray.setFromCamera(mouse,camera);const hit=ray.intersectObjects([...clickables,door],false)[0];renderer.domElement.style.cursor=hit?'pointer':'grab';if(hit){tooltip.style.display='block';tooltip.textContent=hit.object.userData.label;tooltip.style.left=e.clientX+14+'px';tooltip.style.top=e.clientY+14+'px'}else tooltip.style.display='none'});renderer.domElement.addEventListener('click',()=>{ray.setFromCamera(mouse,camera);const hit=ray.intersectObjects([...clickables,door],false)[0];if(hit)setControl(hit.object.userData.control)});
let modelReady=false;
new GLTFLoader().load('assets/boeing-747-300.glb',gltf=>{
  const model=gltf.scene, initial=new THREE.Box3().setFromObject(model), size=initial.getSize(new THREE.Vector3());
  if(size.x>size.z) model.rotation.y=Math.PI/2;
  const rotated=new THREE.Box3().setFromObject(model), extent=rotated.getSize(new THREE.Vector3()), scale=58/Math.max(extent.x,extent.z);
  model.scale.setScalar(scale);model.updateMatrixWorld(true);
  const fitted=new THREE.Box3().setFromObject(model), center=fitted.getCenter(new THREE.Vector3());
  model.position.set(-center.x,-fitted.min.y-3,-center.z);
  model.traverse(o=>{if(o.isMesh){o.castShadow=true;o.receiveShadow=true}}); importedAircraft.add(model);modelReady=true;aircraft.visible=false;
  document.querySelector('#hint').textContent='Exterior model loaded · Drag to orbit · Click controls at right';
},undefined,err=>{console.warn('Exterior GLB failed to load; using procedural fallback.',err);document.querySelector('#hint').textContent='Using procedural exterior fallback'});
const views={cockpit:{p:[0,1.2,3.8],t:[0,.8,-2]},external:{p:[46,25,54],t:[0,0,2]}};function view(n){const outside=n==='external';cockpit.visible=!outside;aircraft.visible=outside&&!modelReady;importedAircraft.visible=outside;exteriorAnimated.visible=outside;camera.position.fromArray(views[n].p);controls.target.fromArray(views[n].t);controls.update();document.querySelectorAll('[data-view]').forEach(b=>b.classList.toggle('active',b.dataset.view===n))}document.querySelectorAll('[data-view]').forEach(b=>b.onclick=()=>view(b.dataset.view));document.querySelector('#reset').onclick=()=>{Object.keys(state).forEach(k=>setControl(k,k==='gear'));view('cockpit')};view('cockpit');
let last=performance.now();function animate(now){requestAnimationFrame(animate);const dt=Math.min((now-last)/1000,.05);last=now;gear.forEach(g=>{const t=g.userData.target??0;g.rotation.z=THREE.MathUtils.damp(g.rotation.z,t,3,dt)});const d=door.userData.target??0;door.rotation.y=THREE.MathUtils.damp(door.rotation.y,-d*1.65,3,dt);door.position.x=-3.88-d*.75;beacon.intensity=state.beacon&&Math.sin(now*.008)>0?28:0;controls.update();renderer.render(scene,camera)}animate(last);
addEventListener('resize',()=>{camera.aspect=mount.clientWidth/mount.clientHeight;camera.updateProjectionMatrix();renderer.setSize(mount.clientWidth,mount.clientHeight)});setTimeout(()=>document.querySelector('#hint').classList.add('hide'),5000);
</script>
</body></html>
