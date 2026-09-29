var Obj = {};

Obj.setup = function() {
	var canvas = document.createElement('canvas');
	canvas.width = window.innerWidth;
	canvas.height = window.innerHeight;
	this.canvas = canvas;
	document.body.appendChild(canvas);
	this.ctx = canvas.getContext('2d');
	this.width = canvas.width;
	this.height = canvas.height;
	this.dataToImageRatio = 1;
	this.ctx.imageSmoothingEnabled = false;
	this.xC = this.width / 2;
	this.yC = this.height / 2;

	this.stepCount = 0;
	this.particles = [];
	this.lifespan = 100;
	this.popPerBirth = 1;
	this.maxPop = 300;
	this.birthFreq = 1;
	this.gridSize = 8;
	
	this.gridSteps = Math.floor(1000 / this.gridSize);
	this.grid = [];
	var i = 0;

	for (var xx = -500; xx < 500; xx += this.gridSize) {
		for (var yy = -500; yy < 500; yy += this.gridSize) {
			var r = Math.sqrt(xx * xx + yy * yy);
			var r0 = 5;
			var field;
			if (r < r0) {
				field = 255 / r0 * r;
			} else if (r > r0) {
				field = 255 - Math.min(255, (r - r0) / 2);
			} else {
				field = 255;
			}
			this.grid.push({
				x: xx,
				y: yy,
				busyAge: 0,
				spotIndex: i,
				isEdge:
				xx == -500
					? 'left'
					: (
					xx == (-500 + this.gridSize * (this.gridSteps - 1))
						? 'right'
						: (
						yy == -500
							? 'top'
							: (
							yy == (-500 + this.gridSize * (this.gridSteps - 1))
								? 'bottom'
								: false
							)
						)
					),
				field: field
			});
			i++;
		}
	}
	this.gridMaxIndex = i;
	this.drawnInLastFrame = 0;
	this.deathCount = 0;
	this.initDraw();
};
Obj.evolve = function() {
	this.stepCount++;
	this.grid.forEach(function(e) {
		if (e.busyAge > 0) {
		e.busyAge++;
		}
	});
	if (
		this.stepCount % this.birthFreq == 0 &&
		(this.particles.length + this.popPerBirth) < this.maxPop
	) {
		this.birth();
	}
	this.move();
	this.draw();
};
Obj.birth = function() {
	var gridSpotIndex =
		Math.floor(Math.random() * this.gridMaxIndex);
	var gridSpot = this.grid[gridSpotIndex];
	var particle = {
		hue: 200,
		sat: 95,
		lum: 20 + Math.floor(40 * Math.random()),
		x: gridSpot.x,
		y: gridSpot.y,
		xLast: gridSpot.x,
		yLast: gridSpot.y,
		xSpeed: 0,
		ySpeed: 0,
		age: 0,
		ageSinceStuck: 0,
		attractor: {
		oldIndex: gridSpotIndex,
		gridSpotIndex: gridSpotIndex
		},
		name: 'seed-' + Math.ceil(10000000 * Math.random())
	};
	this.particles.push(particle);
};

Obj.kill = function(particleName) {
	this.particles = this.particles.filter(function(particle) {
		return particle.name !== particleName;
	});
};

Obj.move = function() {
	for (var i = 0; i < this.particles.length; i++) {
		var p = this.particles[i];
		p.xLast = p.x;
		p.yLast = p.y;
		var index = p.attractor.gridSpotIndex;
		var gridSpot = this.grid[index];
		if (Math.random() < 0.5) {
		if (!gridSpot.isEdge) {
			var topIndex = index - 1;
			var bottomIndex = index + 1;
			var leftIndex = index - this.gridSteps;
			var rightIndex = index + this.gridSteps;
			var topSpot = this.grid[topIndex];
			var bottomSpot = this.grid[bottomIndex];
			var leftSpot = this.grid[leftIndex];
			var rightSpot = this.grid[rightIndex];
			var neighbours = [
			topSpot,
			bottomSpot,
			leftSpot,
			rightSpot
			];
			var chaos = 30;
			var maxFieldSpot = neighbours[0];
			var maxValue =
			maxFieldSpot.field +
			chaos * Math.random();
			for (var n = 1; n < neighbours.length; n++) {
			var candidate = neighbours[n];
			var value =
				candidate.field +
				chaos * Math.random();
			if (value > maxValue) {
				maxValue = value;
				maxFieldSpot = candidate;
			}
			}
			var potentialNewGridSpot = maxFieldSpot;
			if (
			potentialNewGridSpot.busyAge == 0 ||
			potentialNewGridSpot.busyAge > 15
			) {
			p.ageSinceStuck = 0;
			p.attractor.oldIndex = index;
			p.attractor.gridSpotIndex =
				potentialNewGridSpot.spotIndex;
			gridSpot = potentialNewGridSpot;
			gridSpot.busyAge = 1;
			} else {
			p.ageSinceStuck++;
			}
		} else {
			p.ageSinceStuck++;
		}
		if (p.ageSinceStuck == 10) {
			this.kill(p.name);
			continue;
		}
		}
		var k = 8;
		var visc = 0.4;
		var dx = p.x - gridSpot.x;
		var dy = p.y - gridSpot.y;
		var dist = Math.sqrt(dx * dx + dy * dy);
		var xAcc = -k * dx;
		var yAcc = -k * dy;
		p.xSpeed += xAcc;
		p.ySpeed += yAcc;
		p.xSpeed *= visc;
		p.ySpeed *= visc;
		p.speed =
		Math.sqrt(
			p.xSpeed * p.xSpeed +
			p.ySpeed * p.ySpeed
		);
		p.dist = dist;
		p.x += 0.0 * p.xSpeed;
		p.y += 0.0 * p.ySpeed;
		p.age++;
		if (p.age > this.lifespan) {
		this.kill(p.name);
		this.deathCount++;
		}
	}
};

Obj.initDraw = function() {
	this.ctx.beginPath();
	this.ctx.rect(
		0,
		0,
		this.width,
		this.height
	);
	this.ctx.fillStyle = 'rgba(39, 55, 71, 1)';
	this.ctx.fill();
	this.ctx.closePath();
};

Obj.draw = function() {
	this.drawnInLastFrame = 0;
	if (!this.particles.length) {
		return false;
	}
	this.ctx.beginPath();
	this.ctx.rect(
		0,
		0,
		this.width,
		this.height
	);
	this.ctx.fillStyle = 'rgba(39, 55, 71, 0.1)';
	this.ctx.fill();
	this.ctx.closePath();

	for (
		var i = 0;
		i < this.particles.length;
		i++
	) {
		var p = this.particles[i];
		var h;
		var s;
		var l;
		var a;
		h = p.hue + this.stepCount / 30;
		s = p.sat;
		l = p.lum;
		a = 1;
		var last =
			this.dataXYtoCanvasXY(
				p.xLast,
				p.yLast
			);
		var now =
			this.dataXYtoCanvasXY(
				p.x,
				p.y
			);
		var attracSpot =
			this.grid[p.attractor.gridSpotIndex];
		var attracXY =
			this.dataXYtoCanvasXY(
				attracSpot.x,
				attracSpot.y
			);
		var oldAttracSpot =
			this.grid[p.attractor.oldIndex];
		var oldAttracXY =
			this.dataXYtoCanvasXY(
				oldAttracSpot.x,
				oldAttracSpot.y
			);
		this.ctx.beginPath();
		this.ctx.strokeStyle =
			'hsla(' +
				h + ', ' +
				s + '%, ' +
				l + '%, ' +
				a +
			')';
		this.ctx.fillStyle =
			'hsla(' +
				h + ', ' +
				s + '%, ' +
				l + '%, ' +
				a +
			')';
		this.ctx.moveTo(
			last.x,
			last.y
		);
		this.ctx.lineTo(
			now.x,
			now.y
		);
		this.ctx.lineWidth =
			1.5 * this.dataToImageRatio;
		this.ctx.stroke();
		this.ctx.closePath();
		this.ctx.beginPath();
		this.ctx.lineWidth =
			1.5 * this.dataToImageRatio;
		this.ctx.moveTo(
			oldAttracXY.x,
			oldAttracXY.y
		);
		this.ctx.lineTo(
			attracXY.x,
			attracXY.y
		);
		this.ctx.strokeStyle =
			'hsla(' +
				h + ', ' +
				s + '%, ' +
				l + '%, ' +
				a +
			')';
		this.ctx.fillStyle =
			'hsla(' +
				h + ', ' +
				s + '%, ' +
				l + '%, ' +
				a +
			')';
		this.ctx.stroke();
		this.ctx.fill();
		this.ctx.closePath();
		this.drawnInLastFrame++;
	}
};

Obj.dataXYtoCanvasXY = function(x, y) {
	var zoom = 1.6;
	var xx =
		this.xC +
		x * zoom *
		this.dataToImageRatio;
	var yy =
		this.yC +
		y * zoom *
		this.dataToImageRatio;
	return {
		x: xx,
		y: yy
	};
};

document.addEventListener(
	'DOMContentLoaded',
	function() {
		Obj.setup();
		Obj.draw();
		var frame = function() {
			Obj.evolve();
			setTimeout(function() {
				requestAnimationFrame(frame);
			}, 100);
		};
		frame();
	}
);